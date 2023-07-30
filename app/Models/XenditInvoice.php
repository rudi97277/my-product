<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XenditInvoice extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'id',
        'transaction_id',
        'invoice_url',
        'status'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
