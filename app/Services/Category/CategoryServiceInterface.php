<?php

declare(strict_types=1);

namespace App\Services\Category;

use App\Models\Category;

interface CategoryServiceInterface
{
    public function createCategory(string $categoryName): Category;
}
