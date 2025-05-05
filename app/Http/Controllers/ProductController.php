<?php

namespace App\Http\Controllers;

use App\Exceptions\JsonNotFoundException;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use OpenApi\Attributes as OA;


class ProductController extends Controller
{

    public function index()
    {
        $products = Product::with('category')->get();
        return ProductResource::collection($products);
    }


    public function show(string $id)
    {
        return new ProductResource(
            Product::with('category')->find($id)
            ?? throw new JsonNotFoundException()
        );
    }
}
