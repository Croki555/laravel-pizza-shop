<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_products()
    {
        $category = Category::factory()->create();

        Product::factory(5)
            ->pizza()
            ->forCategory($category)
            ->create();

        $response = $this->getJson(route('products.index'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'name',
                        'description',
                        'price',
                        'category',
                    ],
                ],
            ])
            ->assertJson([
                'message' => 'Продукты',
            ])->assertJsonCount(5, 'data');

    }



    /** @test */
    public function user_can_find_product_by_id()
    {
        $category = Category::factory()->create();


        Product::factory(5)
            ->pizza()
            ->forCategory($category)
            ->create();

        $response = $this->getJson(route('products.index'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'name',
                        'description',
                        'price',
                        'category',
                    ],
                ],
            ])
            ->assertJson([
                'message' => 'Продукты',
            ])->assertJsonCount(5, 'data');

    }


    /** @test */
    public function user_can_get_product_by_id()
    {
        $category = Category::factory()->create(['name' => 'Пицца']);
        $product = Product::factory()
            ->pizza()
            ->forCategory($category)
            ->create([
                'name' => 'Гавайская',
                'description' => 'Пицца с курицей, ананасами и сыром моцарелла',
                'price' => 533.00,
            ]);

        $response = $this->getJson("/api/products/{$product->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'name',
                    'description',
                    'price',
                    'category',
                ],
            ])
            ->assertJson([
                'message' => "Продукт по ID - {$product->id}",
                'data' => [
                    'name' => 'Гавайская',
                    'description' => 'Пицца с курицей, ананасами и сыром моцарелла',
                    'price' => 533.00,
                    'category' => 'Пицца',
                ],
            ]);
    }



    /** @test */
    public function user_gets_404_when_product_not_found()
    {

        $category = Category::factory()->create(['name' => 'Пицца']);
        $product = Product::factory()
            ->pizza()
            ->forCategory($category)
            ->create([
                'name' => 'Гавайская',
                'description' => 'Пицца с курицей, ананасами и сыром моцарелла',
                'price' => 533.00,
            ]);

        $response = $this->getJson('/api/products/2')
            ->assertStatus(404)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'id' => [],
                ],
            ])
            ->assertJson([
                'message' => 'Продукт не найден',
                'errors' => [
                    'id' => [
                        'Запрашиваемый ресурс не существует',
                    ],
                ],
            ]);
    }


    /** @test */
    public function admin_can_create_new_product_with_new_category()
    {
        $admin = User::factory()->admin()
            ->create([
                'name' => 'Admin',
                'email' => 'admin@mail.ru',
                'password' => 'admin',
            ]);

        $loginResponse = $this->postJson(route('login'), [
            'email' => 'admin@mail.ru',
            'password' => 'admin',
        ]);

        $token = $loginResponse->json('token');


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('products.store'), [
            'name' => 'Пепперони',
            'description' => 'Классическая пицца с острой колбаской пепперони и сыром моцарелла',
            'category' => "Пицца",
            'price' => 550.35,
        ]);

        $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'name',
                'description',
                'price',
                'category',
            ],
        ])
            ->assertJson([
                "message" => "Продукт успешно создан",
                'data' => [
                    'name' => 'Пепперони',
                    'description' => 'Классическая пицца с острой колбаской пепперони и сыром моцарелла',
                    'price' => 550.35,
                    'category' => 'Пицца',
                ],
            ]);
    }



    /** @test */
    public function admin_can_create_product_with_existing_category()
    {
        $category = Category::factory()->create(['name' => 'Пицца']);

        $admin = User::factory()->admin()
            ->create([
                'name' => 'Admin',
                'email' => 'admin@mail.ru',
                'password' => 'admin',
            ]);

        $loginResponse = $this->postJson(route('login'), [
            'email' => 'admin@mail.ru',
            'password' => 'admin',
        ]);

        $token = $loginResponse->json('token');


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('products.store'), [
            'name' => 'Пепперони',
            'description' => 'Классическая пицца с острой колбаской пепперони и сыром моцарелла',
            'category_id' => $category->id,
            'price' => 550.35,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'name',
                    'description',
                    'price',
                    'category',
                ],
            ])
            ->assertJson([
                "message" => "Продукт успешно создан",
                'data' => [
                    'name' => 'Пепперони',
                    'description' => 'Классическая пицца с острой колбаской пепперони и сыром моцарелла',
                    'price' => 550.35,
                    'category' => 'Пицца',
                ],
            ]);
    }


    /** @test */
    public function admin_cannot_create_product_with_existing_category_name()
    {
        $existingCategory = Category::factory()->create(['name' => 'Пицца']);

        $admin = User::factory()->admin()->create([
            'email' => 'admin@mail.ru',
            'password' => 'admin',
        ]);

        $token = $this->postJson(route('login'), [
            'email' => 'admin@mail.ru',
            'password' => 'admin',
        ])->json('token');


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('products.store'), [
            'name' => 'Пепперони',
            'description' => 'Классическая пицца с острой колбаской пепперони и сыром моцарелла',
            'category' => 'Пицца',
            'price' => 550.35,
        ]);


        $response->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'category',
            ],
        ])
            ->assertJson([
                'message' => 'Категория "Пицца" уже существует',
                'errors' => [
                    'category' => [
                        'Категория "Пицца" уже существует',
                    ],
                ],
            ]);
    }



    /** @test */
    public function admin_cannot_create_product_with_nonexistent_category_id()
    {
        $categories = Category::factory()->count(2)->sequence(
            ['name' => 'Пицца'],
            ['name' => 'Напиток']
        )->create();

        $admin = User::factory()->admin()->create([
            'email' => 'admin@mail.ru',
            'password' => 'admin',
        ]);

        $token = $this->postJson(route('login'), [
            'email' => 'admin@mail.ru',
            'password' => 'admin',
        ])->json('token');


        $nonExistentId = Category::max('id') + 1;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('products.store'), [
            'name' => 'Новый продукт',
            'price' => 100,
            'description' => 'Описание продукта',
            'category_id' => $nonExistentId,
        ]);


        $availableCategories = Category::all()
            ->map(fn ($cat) => "#{$cat->id} - {$cat->name}")
            ->implode(', ');


        $response->assertStatus(422)
            ->assertJson([
                'message' => "Категория с ID {$nonExistentId} не найдена. Доступные категории: {$availableCategories}",
                'errors' => [
                    'category_id' => [
                        "Категория с ID {$nonExistentId} не найдена. Доступные категории: {$availableCategories}",
                    ],
                ],
            ]);

    }



    /** @test */
    public function admin_can_update_existing_product()
    {
        $category = Category::factory()->create(['name' => 'Пицца']);
        $product = Product::factory()
            ->forCategory($category)
            ->create([
                'name' => 'Пепперони',
                'description' => 'Классическая пицца',
                'price' => 500.00,
            ]);

        $admin = User::factory()->admin()->create([
            'email' => 'admin@mail.ru',
            'password' => 'admin',
        ]);

        $token = $this->postJson(route('login'), [
            'email' => 'admin@mail.ru',
            'password' => 'admin',
        ])->json('token');


        $updateData = [
            'price' => 332,
            'description' => 'клюквенное описание',
        ];


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/admin/products/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'name',
                    'description',
                    'price',
                    'category',
                ],
            ])
            ->assertJson([
                'message' => 'Продукт успешно изменен',
                'data' => [
                    'name' => 'Пепперони',
                    'description' => 'клюквенное описание',
                    'price' => 332,
                    'category' => 'Пицца',
                ],
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'description' => 'клюквенное описание',
            'price' => 332,
        ]);
    }



    /** @test */
    public function admin_can_delete_product()
    {
        $category = Category::factory()->create(['name' => 'Пицца']);
        $product = Product::factory()
            ->forCategory($category)
            ->create([
                'name' => 'Пепперони',
                'description' => 'Классическая пицца',
                'price' => 500.00,
            ]);

        $admin = User::factory()->admin()->create([
            'email' => 'admin@mail.ru',
            'password' => 'admin',
        ]);

        $token = $this->postJson(route('login'), [
            'email' => 'admin@mail.ru',
            'password' => 'admin',
        ])->json('token');


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/admin/products/{$product->id}");


        $response->assertStatus(204)
            ->assertNoContent();

    }
}
