<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserSimpleResource($this->user),
            'product' => new ProductResource($this->product),
            'price' => $this->price,
            'quantity' => $this->quantity,
            'grand_total' => $this->grand_total,
            'rating' => $this->rating,
            'status' => $this->status,
            'xendit_invoice' => new XenditInvoiceResource($this->whenLoaded('xenditInvoice'))
        ];
    }
}
