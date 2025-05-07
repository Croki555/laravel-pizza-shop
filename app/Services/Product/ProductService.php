<?php

namespace App\Services\Product;

use App\Exceptions\JsonNotFoundException;
use App\Models\Product;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Services\Category\CategoryServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductService implements ProductServiceInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CategoryServiceInterface $categoryService,
    ){}

    public function getProducts(): Collection
    {
        return $this->productRepository->getAllWithCategories();
    }

    public function getProductById(int $id): Product
    {
        $product = $this->productRepository->findByIdWithCategory($id);

        if (!$product) {
            throw new JsonNotFoundException('Продукт не найден');
        }

        return $product;
    }

    public function addProduct(array $data): Product
    {
        if (isset($data['category'])) {
            $data['category_id'] = $this->categoryService->createCategory($data['category'])->id;
        }

        return $this->productRepository->createProduct($data);
    }
}
