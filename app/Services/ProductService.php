<?php

namespace App\Services;

use App\Models\Product;
use App\Traits\UserInfo;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    use UserInfo;

    public function getAllProducts($request)
    {
        return Product::when($request->keyword, fn ($query) => $query->where('name', 'LIKE', "%$request->keyword%"))
            ->paginate($request->input('page_size', 10));
    }

    public function createNewProduct($request, ImageUploadService $imageUploadService)
    {
        $data = $request->only('name', 'description', 'price', 'quantity');
        $data['created_by'] = $this->getCurrentUser()->id;
        $data['image_url'] = $imageUploadService->uploadImage($request->image);
        $product =  Product::create($data);
        $product->categories()->attach($request->product_category_ids);
        return $product;
    }

    public function getProductById($id)
    {
        return Product::findOrFail($id);
    }

    public function getProductByIdStrict($id)
    {
        $user = $this->getCurrentUser();
        return Product::where('created_by', $user->id)->findOrFail($id);
    }

    public function updateProductById($request, $id, ImageUploadService $imageUploadService)
    {
        $product = $this->getProductByIdStrict($id);
        $data = $request->only('name', 'description', 'price', 'quantity', 'is_active');

        if ($request->image) {
            if (Storage::disk('public')->exists($product->image_url))
                Storage::disk('public')->delete($product->image_url);
            $data['image_url'] = $imageUploadService->uploadImage($request->image);
        }

        if ($request->product_category_ids)
            $product->categories()->sync($request->product_category_ids);

        return $product->update($data);
    }

    public function deleteProductById($id)
    {
        $product = $this->getProductByIdStrict($id);
        $product->categories()->detach();

        if (Storage::disk('public')->exists($product->image_url))
            Storage::disk('public')->delete($product->image_url);

        return $product->delete();
    }
}
