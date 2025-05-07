<?php

namespace App\Repositories\Product;

use App\Exceptions\JsonNotFoundException;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllWithCategories(): Collection
    {
        return Product::with('category')->get();
    }

    public function findByIdWithCategory(int $id): ?Product
    {
        return Product::with('category')->find($id);
    }

    public function createProduct(array $data): Product
    {
        return Product::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'category_id' => $data['category_id'],
        ]);
    }
}
