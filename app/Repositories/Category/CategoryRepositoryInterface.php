<?php

declare(strict_types=1);

namespace App\Repositories\Category;

use App\Models\Category;

interface CategoryRepositoryInterface
{
    public function createCategory(string $categoryName): Category;
}
