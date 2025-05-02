<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insertOrIgnore([
            // Пиццы (category_id = 1)
            [
                'name' => 'Пепперони',
                'description' => 'Классическая пицца с острой колбаской пепперони и сыром моцарелла',
                'category_id' => 1,
                'price' => fake()->randomElement([
                    fake()->numberBetween(400, 600),
                    fake()->randomFloat(2, 400, 600),
                ]),
                'created_at' => now(),
            ],
            [
                'name' => 'Маргарита',
                'description' => 'Традиционная итальянская пицца с томатным соусом, моцареллой и базиликом',
                'category_id' => 1,
                'price' => fake()->randomElement([
                    fake()->numberBetween(400, 600),
                    fake()->randomFloat(2, 400, 600),
                ]),
                'created_at' => now(),
            ],
            [
                'name' => 'Гавайская',
                'description' => 'Пицца с курицей, ананасами и сыром моцарелла',
                'category_id' => 1,
                'price' => fake()->randomElement([
                    fake()->numberBetween(400, 600),
                    fake()->randomFloat(2, 400, 600),
                ]),
                'created_at' => now(),
            ],
            [
                'name' => 'Четыре сыра',
                'description' => 'Пицца с сочетанием моцареллы, горгонзолы, пармезана и эмменталя',
                'category_id' => 1,
                'price' => fake()->randomElement([
                    fake()->numberBetween(400, 600),
                    fake()->randomFloat(2, 400, 600),
                ]),
                'created_at' => now(),
            ],
            [
                'name' => 'Мясная',
                'description' => 'Сытная пицца с ветчиной, пепперони, беконом и говядиной',
                'category_id' => 1,
                'price' => fake()->randomElement([
                    fake()->numberBetween(400, 600),
                    fake()->randomFloat(2, 400, 600),
                ]),
                'created_at' => now(),
            ],

            // Напитки (category_id = 2)
            [
                'name' => 'Кола',
                'description' => 'Классический газированный напиток',
                'category_id' => 2,
                'price' => fake()->randomElement([
                    fake()->numberBetween(400, 600),
                    fake()->randomFloat(2, 400, 600),
                ]),
                'created_at' => now(),
            ],
            [
                'name' => 'Фанта',
                'description' => 'Апельсиновый газированный напиток',
                'category_id' => 2,
                'price' => fake()->randomElement([
                    fake()->numberBetween(400, 600),
                    fake()->randomFloat(2, 400, 600),
                ]),
                'created_at' => now(),
            ],
            [
                'name' => 'Спрайт',
                'description' => 'Лимонно-лаймовый газированный напиток',
                'category_id' => 2,
                'price' => fake()->randomElement([
                    fake()->numberBetween(400, 600),
                    fake()->randomFloat(2, 400, 600),
                ]),
                'created_at' => now(),
            ],
            [
                'name' => 'Чай зеленый',
                'description' => 'Освежающий холодный зеленый чай',
                'category_id' => 2,
                'price' => fake()->randomElement([
                    fake()->numberBetween(400, 600),
                    fake()->randomFloat(2, 400, 600),
                ]),
                'created_at' => now(),
            ],
            [
                'name' => 'Морс клюквенный',
                'description' => 'Натуральный клюквенный морс',
                'category_id' => 2,
                'price' => fake()->randomElement([
                    fake()->numberBetween(400, 600),
                    fake()->randomFloat(2, 400, 600),
                ]),
                'created_at' => now(),
            ]
        ]);
    }
}
