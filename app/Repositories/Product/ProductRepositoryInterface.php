<?php

namespace App\Repositories\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function getAllWithCategories(): Collection;
    public function findByIdWithCategory(int $id): ?Product;
    public function createProduct(array $data): Product;
}
