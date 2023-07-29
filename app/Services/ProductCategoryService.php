<?php

namespace App\Services;

use App\Models\ProductCategory;
use App\Traits\UserInfo;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductCategoryService
{
    use UserInfo;

    public function getAllProductCategories($request)
    {
        return ProductCategory::paginate($request->input('page_size', 10));
    }
    public function createNewProductCategory($request, ImageUploadService $imageUploadService)
    {
        $userId = $this->getCurrentUser()->id;
        $data = $request->only('name', 'description');
        $data['is_active'] = true;
        $data['created_by'] = $userId;
        $data['image_url'] = $imageUploadService->uploadImage($request->image);

        return ProductCategory::create($data);
    }

    public function getProductCategoryById($id)
    {
        return ProductCategory::findOrFail($id);
    }

    public function getProductCategoryByIdStrict($id)
    {
        $user = $this->getCurrentUser();
        return ProductCategory::where('created_by', $user->id)->findOrFail($id);
    }

    public function updateProductCategoryById($request, $id, ImageUploadService $imageUploadService)
    {
        $productCategory = $this->getProductCategoryByIdStrict($id);
        $data = $request->only('name', 'description', 'is_active');

        if ($request->image) {
            if (Storage::disk('public')->exists($productCategory->image_url))
                Storage::disk('public')->delete($productCategory->image_url);
            $data['image_url'] = $imageUploadService->uploadImage($request->image);
        }
        return $productCategory->update($data);
    }

    public function deleteProductCategoryById($id)
    {
        $productCategory = $this->getProductCategoryByIdStrict($id);
        if (Storage::disk('public')->exists($productCategory->image_url))
            Storage::disk('public')->delete($productCategory->image_url);
        return $productCategory->delete();
    }
}
