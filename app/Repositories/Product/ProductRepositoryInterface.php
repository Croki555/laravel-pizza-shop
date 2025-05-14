<?php

declare(strict_types=1);

namespace App\Repositories\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    /**
     * @return Collection<int, Product>
     */
    public function getAllWithCategories(): Collection;

    /**
     * @param int $id
     * @return Product|null
     */
    public function findByIdWithCategory(int $id): ?Product;

    /**
     * @param array<string, mixed> $data
     * @return Product
     */
    public function createProduct(array $data): Product;

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return Product|null
     */
    public function updateProduct(int $id, array $data): ?Product;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteProduct(int $id): bool;
}
