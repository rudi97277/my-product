<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string',
            'description' => 'string',
            'price' => 'integer',
            'quantity' => 'integer',
            'is_active' => 'boolean',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'product_category_ids' => 'array',
            'product_category_ids.*' => 'exists:product_categories,id'
        ];
    }
}
