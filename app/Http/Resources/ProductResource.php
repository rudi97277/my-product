<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'image_url' => url("storage/" . $this->image_url),
            'description' => $this->description,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'is_active' => $this->is_active,
            'product_categories' => ProductCategoryResource::collection($this->whenLoaded('categories')),
            'created_by' => new UserSimpleResource($this->whenLoaded('createdBy'))
        ];
    }
}
