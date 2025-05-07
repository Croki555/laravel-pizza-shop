<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\Product\ProductServiceInterface;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductServiceInterface $productService
    ) {}

    public function index(): JsonResponse
    {
        $products = $this->productService->getProducts();
        return response()->json(ProductResource::collection($products));
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);
        return response()->json(new ProductResource($product));
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
       $product = $this->productService->addProduct($request->validated());
        return response()->json([
            'message' => 'Продукт успешно создан',
            'data' => new ProductResource($product)
        ]);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->updateProduct($id, $request->validated());

        return response()->json(new ProductResource($product));
    }

}
