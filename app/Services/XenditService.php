<?php

namespace App\Services;

use App\Models\XenditInvoice;
use Xendit\Invoice;


class XenditService
{
    public function createInvoice($externalId, $userEmail, $amount, $description = '')
    {
        $invoice = Invoice::create([
            'external_id' => $externalId,
            'payer_email' => $userEmail,
            'description' => $description,
            'amount' => $amount,
        ]);

        return XenditInvoice::create([
            'id' => $invoice['id'],
            'transaction_id' => $invoice['external_id'],
            'invoice_url' => $invoice['invoice_url'],
            'status' => $invoice['status'],
        ]);
    }
}
