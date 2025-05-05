<?php

namespace App\Http\Controllers;

use App\Exceptions\JsonNotFoundException;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{

    public function index(): JsonResponse
    {
        $products = Product::with('category')->get();
        return response()->json(ProductResource::collection($products));
    }


    public function show(string $id): JsonResponse
    {
        return response()->json(new ProductResource(
            Product::with('category')->find($id)
            ?? throw new JsonNotFoundException()
        ));
    }
}
