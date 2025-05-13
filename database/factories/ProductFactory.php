<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->sentence(10),
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'category_id' => Category::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }


    public function forCategory(Category $category)
    {
        return $this->state([
            'category_id' => $category->id,
        ]);
    }


    public function pizza()
    {
        return $this->state([
            'name' => $this->faker->randomElement(['Пепперони', 'Маргарита', 'Гавайская', 'Четыре сыра', 'Мясная']),
            'price' => $this->faker->numberBetween(400, 800),
        ]);
    }

    public function drink()
    {
        return $this->state([
            'name' => $this->faker->randomElement(['Кола', 'Фанта', 'Спрайт', 'Чай', 'Морс']),
            'price' => $this->faker->numberBetween(100, 300),
        ]);
    }
}
