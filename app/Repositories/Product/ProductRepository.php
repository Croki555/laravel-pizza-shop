<?php

declare(strict_types=1);

namespace App\Repositories\Product;

use App\Exceptions\JsonNotFoundException;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @return Collection<int, Product>
     */
    public function getAllWithCategories(): Collection
    {
        return Product::with('category')->get();
    }

    /**
     * @param int $id
     * @return Product|null
     */
    public function findByIdWithCategory(int $id): ?Product
    {
        return Product::with('category')->find($id);
    }

    /**
     * @param array<string, mixed> $data
     * @return Product
     */
    public function createProduct(array $data): Product
    {
        $product = Product::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'category_id' => $data['category_id'],
        ]);

        return $product->load('category');
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return Product|null
     */
    public function updateProduct(int $id, array $data): ?Product
    {
        $product = Product::find($id);

        if (!$product) {
            return null;
        }

        $product->update($data);

        return $product->load('category');
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteProduct(int $id): bool
    {
        return Product::where('id', $id)->delete() > 0;
    }
}
