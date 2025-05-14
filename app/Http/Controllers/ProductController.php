<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Services\Product\ProductServiceInterface;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductServiceInterface $productService
    ) {
    }

    public function index(): JsonResponse
    {
        $products = $this->productService->getProducts();

        return response()->json([
            'message' => "Продукты",
            'data' => ProductResource::collection($products),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        return response()->json([
            'message' => "Продукт по ID - ${id}",
            'data' => new ProductResource($product),
        ]);
    }
}
