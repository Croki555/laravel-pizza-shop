<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductServiceInterface
{
    public function getProducts(): Collection;
    public function getProductById(int $id): Product;
    public function addProduct(array $data): Product;
    public function updateProduct(int $id, array $data): Product;
    public function deleteProduct(int $id): bool;

}
