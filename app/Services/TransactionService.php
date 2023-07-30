<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\XenditInvoice;
use App\Traits\UserInfo;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TransactionService
{
    use UserInfo;

    public function getAllMyTransaction($request)
    {
        $user = $this->getCurrentUser();
        return Transaction::where('user_id', $user->id)
            ->paginate($request->input('page_size', 10));
    }

    public function createNewTransaction($request, XenditService $xenditService)
    {
        $user = $this->getCurrentUser();
        $product = $this->checkProduct($request->product_id, $request->quantity);
        $data = [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => $product->price,
            'quantity' => $request->quantity,
            'grand_total' => $product->price * $request->quantity,
        ];

        DB::beginTransaction();

        try {
            $product->quantity = $product->quantity - $request->quantity;
            $product->save();
            $transaction = Transaction::create($data);

            $xenditInvoice = $xenditService->createInvoice($transaction->id, $user->email, $transaction->grand_total, 'User buy');
            DB::commit();
            return $xenditInvoice;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new HttpException(500, $th);
        }
    }

    public function checkProduct($id, $quantity)
    {
        $product = Product::findOrFail($id);

        if ($product->quantity < $quantity)
            throw new HttpException(400, 'Not enough quantity');

        if (!$product->is_active)
            throw new HttpException(400, 'Inactive product');

        if ($product->created_by == $this->getCurrentUser()->id)
            throw new HttpException(400, 'Cannot buy your own product');

        return $product;
    }

    public function getTransactionByIdStrict($id)
    {
        $user = $this->getCurrentUser();
        return Transaction::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();
    }

    public function cancelTransactionById($id)
    {
        $transaction = $this->getTransactionByIdStrict($id);
        if ($transaction->status != 'pending')
            throw new HttpException(400, 'Pending transaction only!');
        return $transaction->update(['status' => 'cancelled']);
    }

    public function processXenditCallback($request)
    {
        $xCallbackToken = $request->header('X-CALLBACK-TOKEN');
        $configCallbackToken = config('services.xendit.calback_key');
        if ($xCallbackToken != $configCallbackToken)
            return throw new HttpException(401, 'Unauthorized!');

        $xenditInvoice = XenditInvoice::where('status', 'PENDING')->find($request->id);
        if (!$xenditInvoice)
            return null;
        $xenditInvoice->update(['status' => $request->status]);
        if ($request->status == 'PAID') {
            DB::beginTransaction();
            try {
                $transaction = $xenditInvoice->transaction;
                $user = $transaction->product->createdBy;
                $transaction->update(['status' => 'paid']);
                $user->balance = $user->balance + $transaction->grand_total;
                $user->save();
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                throw new HttpException(500, $th);
            }
        }
        return null;
    }
}
