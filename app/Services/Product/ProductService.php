<?php

declare(strict_types=1);

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


    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->productRepository->getAllWithCategories();
    }

    /**
     * @param int $id
     * @return Product
     * @throws JsonNotFoundException
     */
    public function getProductById(int $id): Product
    {
        $product = $this->productRepository->findByIdWithCategory($id);

        if (!$product) {
            throw new JsonNotFoundException('Продукт не найден');
        }

        return $product;
    }

    /**
     * @param array<string, mixed> $data
     * @return Product
     */
    public function addProduct(array $data): Product
    {
        if (isset($data['category'])) {
            $data['category_id'] = $this->categoryService->createCategory($data['category'])->id;
        }

        return $this->productRepository->createProduct($data);
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return Product
     * @throws JsonNotFoundException
     */
    public function updateProduct(int $id, array $data): Product
    {
        $product = $this->productRepository->updateProduct($id, $data);

        if (!$product) {
            throw new JsonNotFoundException('Продукт не найден');
        }

        return $product;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->deleteProduct($id);
    }
}
