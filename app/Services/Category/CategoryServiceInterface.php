<?php

namespace App\Services\Category;

use App\Models\Category;

interface CategoryServiceInterface
{
    public function createCategory(string $categoryName): Category;
}
