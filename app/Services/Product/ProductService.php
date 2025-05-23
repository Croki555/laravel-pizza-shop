<?php

declare(strict_types=1);

namespace App\Services\Product;

use App\Exceptions\JsonNotFoundException;
use App\Models\Product;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Services\Category\CategoryServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductService implements ProductServiceInterface
{
    private const CACHE_TTL_SECONDS = 86400; // 1 день
    public const PRODUCTS_ALL_CACHE_KEY = 'products:all';
    public const PRODUCT_CACHE_KEY_PREFIX = 'product:';

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CategoryServiceInterface $categoryService,
    ) {
    }


    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return Cache::remember(self::PRODUCTS_ALL_CACHE_KEY, self::CACHE_TTL_SECONDS, function () {
            return $this->productRepository->getAllWithCategories();
        });
    }

    /**
     * @param int $id
     * @return Product
     * @throws JsonNotFoundException
     */
    public function getProductById(int $id): Product
    {
        $cacheKey = self::PRODUCT_CACHE_KEY_PREFIX . $id;

        $product = Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($id) {
            $product = $this->productRepository->findByIdWithCategory($id);

            if (!$product) {
                Log::error('Product not found in database', ['id' => $id]);
                return null;
            }

            return $product;
        });

        if (!$product) {
            Log::error('Product not found in cache or database', [
                'id' => $id,
                'cacheKey' => $cacheKey,
                'cached' => Cache::get($cacheKey)
            ]);
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
