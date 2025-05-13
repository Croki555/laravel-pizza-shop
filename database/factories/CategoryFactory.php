<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected static $mainCategories = [
        'Пицца',
        'Напиток',
    ];

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(self::$mainCategories),
        ];
    }

    /**
     * Категория "Пицца"
     */
    public function pizza()
    {
        return $this->state([
            'name' => 'Пицца',
        ]);
    }

    /**
     * Категория "Напиток"
     */
    public function drink()
    {
        return $this->state([
            'name' => 'Напиток',
        ]);
    }
}
