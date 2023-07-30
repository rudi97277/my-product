<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionMyTransactionRequest;
use App\Http\Requests\TransactionStoreRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\XenditInvoiceResource;
use App\Services\TransactionService;
use App\Services\XenditService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Xendit\Xendit;

class TransactionController extends Controller
{
    use ApiResponser;
    protected $service;
    protected $xenditService;

    public function __construct(TransactionService $transactionService, XenditService $xenditService)
    {
        Xendit::setApiKey(config('services.xendit.api_key'));
        $this->service = $transactionService;
        $this->xenditService = $xenditService;
    }

    public function myTransaction(TransactionMyTransactionRequest $request)
    {
        $transactions = $this->service->getAllMyTransaction($request);
        return $this->showPaginate('transactions', collect(TransactionResource::collection($transactions)), collect($transactions));
    }

    public function store(TransactionStoreRequest $request)
    {
        $invoice = $this->service->createNewTransaction($request, $this->xenditService);
        return $this->showOne(new XenditInvoiceResource($invoice));
    }

    public function show($id)
    {
        $transaction = $this->service->getTransactionByIdStrict($id);
        return $this->showOne(new TransactionResource($transaction->load('xenditInvoice')));
    }

    public function cancel($id)
    {
        $status = $this->service->cancelTransactionById($id);
        return $this->showOne($status);
    }

    public function callback(Request $request)
    {
        $status = $this->service->processXenditCallback($request);
        return $this->showOne($status);
    }
}
