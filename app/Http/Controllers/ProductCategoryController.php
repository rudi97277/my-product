<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCategoryIndexRequest;
use App\Http\Requests\ProductCategoryStoreRequest;
use App\Http\Requests\ProductCategoryUpdateRequest;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use App\Services\ImageUploadService;
use App\Services\ProductCategoryService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{

    use ApiResponser;
    protected $service;
    protected $imageUploadService;

    public function __construct(ProductCategoryService $productCategoryService, ImageUploadService $imageUploadService)
    {
        $this->service = $productCategoryService;
        $this->imageUploadService = $imageUploadService;
    }
    public function index(ProductCategoryIndexRequest $request)
    {
        $productCategories = $this->service->getAllProductCategories($request);
        return $this->showPaginate('product_categories', collect(ProductCategoryResource::collection($productCategories)), collect($productCategories));
    }

    public function store(ProductCategoryStoreRequest $request)
    {
        $productCategory = $this->service->createNewProductCategory($request, $this->imageUploadService);
        return $this->showOne(new ProductCategoryResource($productCategory->load('createdBy')));
    }

    public function show($id)
    {
        $productCategory = $this->service->getProductCategoryById($id);
        return $this->showOne(new ProductCategoryResource($productCategory->load('createdBy')));
    }

    public function update(ProductCategoryUpdateRequest $request, $id)
    {
        $status = $this->service->updateProductCategoryById($request, $id, $this->imageUploadService);
        return $this->showOne($status);
    }

    public function destroy($id)
    {
        $status = $this->service->deleteProductCategoryById($id);
        return $this->showOne($status);
    }
}
