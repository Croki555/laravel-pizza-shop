<?php

declare(strict_types=1);

namespace App\Repositories\Category;

use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function createCategory(string $categoryName): Category
    {
        return Category::create([
            'name' => $categoryName,
        ]);
    }
}
