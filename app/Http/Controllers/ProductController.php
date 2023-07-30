<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductIndexRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ImageUploadService;
use App\Services\ProductService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    use ApiResponser;
    protected $service;
    protected $imageUploadService;

    public function __construct(ProductService $productService, ImageUploadService $imageUploadService)
    {
        $this->service = $productService;
        $this->imageUploadService = $imageUploadService;
    }

    public function index(ProductIndexRequest $request)
    {
        $products = $this->service->getAllProducts($request);
        return $this->showPaginate('products', collect(ProductResource::collection($products)), collect($products));
    }

    public function store(ProductStoreRequest $request)
    {
        $product = $this->service->createNewProduct($request, $this->imageUploadService);
        return $this->showOne(new ProductResource($product->load('createdBy', 'categories')));
    }

    public function show($id)
    {
        $product = $this->service->getProductById($id);
        return $this->showOne(new ProductResource($product->load('createdBy', 'categories')));
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        $status = $this->service->updateProductById($request, $id, $this->imageUploadService);
        return $this->showOne($status);
    }


    public function destroy($id)
    {
        $status = $this->service->deleteProductById($id);
        return $this->showOne($status);
    }
}
