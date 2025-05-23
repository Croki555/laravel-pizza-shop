<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\Product\ProductService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    public function created(Product $product): void
    {
        $this->clearProductCache();
    }

    public function updated(Product $product): void
    {
        $this->clearProductCache();
        $this->clearSingleProductCache($product->id);
    }

    public function deleted(Product $product): void
    {
        $this->clearProductCache();
        $this->clearSingleProductCache($product->id);
    }

    public function restored(Product $product): void
    {
        $this->clearProductCache();
        $this->clearSingleProductCache($product->id);
    }

    public function forceDeleted(Product $product): void
    {
        $this->clearProductCache();
        $this->clearSingleProductCache($product->id);
    }

    private function clearProductCache(): void
    {
        Log::info('Clearing all products cache');
        Cache::forget(ProductService::PRODUCTS_ALL_CACHE_KEY);
    }

    private function clearSingleProductCache(int $productId): void
    {
        Log::info('Attempting to clear product cache', [
            'product_id' => $productId,
            'before' => Cache::get(ProductService::PRODUCT_CACHE_KEY_PREFIX . $productId)
        ]);

        Cache::forget(ProductService::PRODUCT_CACHE_KEY_PREFIX . $productId);

        Log::info('After clearing', [
            'after' => Cache::get(ProductService::PRODUCT_CACHE_KEY_PREFIX . $productId)
        ]);
    }
}
