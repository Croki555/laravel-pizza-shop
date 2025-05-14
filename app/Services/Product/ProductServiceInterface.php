<?php

declare(strict_types=1);

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductServiceInterface
{
    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection;

    /**
     * @param int $id
     * @return Product
     */
    public function getProductById(int $id): Product;

    /**
     * @param array<string, mixed> $data
     * @return Product
     */
    public function addProduct(array $data): Product;

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return Product
     */
    public function updateProduct(int $id, array $data): Product;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteProduct(int $id): bool;

}
