<?php

declare(strict_types=1);

namespace App\Services\Category;

use App\Models\Category;
use App\Repositories\Category\CategoryRepositoryInterface;


class CategoryService implements CategoryServiceInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository
    ){}

    public function createCategory(string $categoryName): Category
    {
        return $this->categoryRepository->createCategory($categoryName);
    }
}
