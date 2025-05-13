<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->pushMiddleware(\Illuminate\Session\Middleware\StartSession::class);
    }

    /** @test */
    public function auth_user_can_create_order(): void
    {
        Status::factory()->create([
            'name'=> 'В работе'
        ]);

        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $token = $this->postJson(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ])->json('token');

        $category = Category::factory()->create(['id' => 1, 'name' => 'Пицца']);
        $pizza = Product::factory()->create([
            'name' => 'Пепперони',
            'description' => 'Классическая пицца с пепперони',
            'category_id' => $category->id,
            'price' => 550.00
        ]);

        $this->postJson(route('cart.add'), [
            'product_id' => $pizza->id,
            'quantity' => 4
        ])->assertStatus(200);


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('order.store'), [
            'email' => 'test3@mail.ru',
            'phone' => '+7 (888) 456-78-91',
            'delivery_address' => 'город Москва, Ленина, 1, кв.1',
            'delivery_time' => '2025-05-05 21:00:00'
        ]);

        $response->assertStatus(201)->assertJsonStructure(['message'])
            ->assertJson(['message' => 'Заказ создан']);
    }



    /** @test */
    public function auth_user_can_see_their_orders(): void
    {
        Status::factory()->create([
            'name'=> 'В работе'
        ]);

        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $token = $this->postJson(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ])->json('token');

        $category = Category::factory()->create(['id' => 1, 'name' => 'Пицца']);
        $pizza = Product::factory()->create([
            'name' => 'Пепперони',
            'description' => 'Классическая пицца с пепперони',
            'category_id' => $category->id,
            'price' => 550.00
        ]);

        $this->postJson(route('cart.add'), [
            'product_id' => $pizza->id,
            'quantity' => 4
        ])->assertStatus(200);

        $order = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('order.store'), [
            'email' => 'test3@mail.ru',
            'phone' => '+7 (888) 456-78-91',
            'delivery_address' => 'город Москва, Ленина, 1, кв.1',
            'delivery_time' => '2025-05-05 21:00:00'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson(route('order.index'));

        $response->assertStatus(200)->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'status',
                    'phone',
                    'delivery_address',
                    'delivery_time',
                    'items' => [
                        '*' => [
                            'name',
                            'price',
                            'quantity'
                        ]
                    ]
                ]
            ]
        ])->assertJson(['message' => 'Ваши заказы']);
    }



    /** @test */
    public function auth_user_cannot_create_order_if_cart_is_empty(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $token = $this->postJson(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ])->json('token');


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('order.store'), [
            'email' => 'test3@mail.ru',
            'phone' => '+7 (888) 456-78-91',
            'delivery_address' => 'город Москва, Ленина, 1, кв.1',
            'delivery_time' => '2025-05-05 21:00:00'
        ]);

        $response->assertStatus(422)->assertJsonStructure(['message'])
            ->assertJson(['message' => 'Добавьте товары в корзину']);
    }



    /** @test */
    public function auth_user_cannot_create_order_if_has_invalid_data(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $token = $this->postJson(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ])->json('token');

        $category = Category::factory()->create(['id' => 1, 'name' => 'Пицца']);
        $pizza = Product::factory()->create([
            'name' => 'Пепперони',
            'description' => 'Классическая пицца с пепперони',
            'category_id' => $category->id,
            'price' => 550.00
        ]);

        $this->postJson(route('cart.add'), [
            'product_id' => $pizza->id,
            'quantity' => 4
        ])->assertStatus(200);


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('order.store'), [
            'email' => 'test3####mail.ru',
            'phone' => '()999 456-78-91',
            'delivery_address' => 'Какой-то адрес',
            'delivery_time' => '2024-05-05 21:00:00'
        ]);

        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'email',
                'phone',
                'delivery_address',
                'delivery_time'
            ]
        ]);
    }


    /** @test */
    public function admin_cannot_create_order(): void
    {

        User::factory()->admin()
            ->create([
                'name' => 'Admin',
                'email'=> 'admin@mail.ru',
                'password' => 'admin'
            ]);

        $token = $this->postJson(route('login'), [
            'email' => 'admin@mail.ru',
            'password' => 'admin',
        ])->json('token');

        $category = Category::factory()->create(['id' => 1, 'name' => 'Пицца']);
        $pizza = Product::factory()->create([
            'name' => 'Пепперони',
            'description' => 'Классическая пицца с пепперони',
            'category_id' => $category->id,
            'price' => 550.00
        ]);

        $this->postJson(route('cart.add'), [
            'product_id' => $pizza->id,
            'quantity' => 4
        ])->assertStatus(200);


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('order.store'), [
            'email' => 'test3@mail.ru',
            'phone' => '+7 (888) 456-78-91',
            'delivery_address' => 'город Москва, Ленина, 1, кв.1',
            'delivery_time' => '2025-05-05 21:00:00'
        ]);

        $response->assertStatus(403)->assertJsonStructure(['message'])
            ->assertJson(['message' => 'Запрещено: только для обычных пользователей']);
    }


    /** @test */
    public function admin_can_see_all_orders_users(): void
    {
        $category = Category::factory()->create(['name' => 'Пицца']);
        $product = Product::factory()->create([
            'name' => 'Пепперони',
            'price' => 550.00,
            'category_id' => $category->id
        ]);

        Status::factory()->create(['id' => 1, 'name' => 'В работе']);

        User::factory()->admin()->create([
            'email' => 'admin@mail.ru',
            'password' => 'admin',
        ]);

        $user = User::factory()->create([
            'email'=> 'test@mail.ru',
            'password' => 'password'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'email' => 'test3@mail.ru',
            'phone' => '+7 (888) 456-78-91',
            'delivery_address' => 'город Москва, Ленина, 1, кв.1',
            'delivery_time' => '2025-05-05 21:00:00',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $orderItems = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);


        $adminToken = $this->postJson(route('login'), [
            'email' => 'admin@mail.ru',
            'password' => 'admin',
        ])->json('token');


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken,
        ])->getJson(route('admin.orders.index'));


        $response->assertStatus(200)->assertJsonStructure([
            'message',
            'total_orders',
            'data' => [
                '*' => [
                    'status',
                    'delivery_address',
                    'delivery_time',
                    'user'
                ]
            ]
        ])->assertJson([
            'message' => 'Заказы пользователей',
            'total_orders' => 1,
            'data' => [
                [
                    'status' => 'В работе',
                    'delivery_address' => 'город Москва, Ленина, 1, кв.1',
                    'delivery_time' => '2025-05-05 21:00',
                    'user' => [
                        'name' => $user->name,
                        'email' => 'test@mail.ru',
                    ]
                ]
            ]
        ]);
    }


    /** @test */
    public function admin_can_edit_status_order_user(): void
    {
        $category = Category::factory()->create(['name' => 'Пицца']);
        $product = Product::factory()->create([
            'name' => 'Пепперони',
            'price' => 550.00,
            'category_id' => $category->id
        ]);

        Status::factory()->create(['id' => 1, 'name' => 'В работе']);
        Status::factory()->create(['id' => 2, 'name' => 'Доставляется']);

        User::factory()->admin()->create([
            'email' => 'admin@mail.ru',
            'password' => 'admin',
        ]);

        $user = User::factory()->create([
            'email'=> 'test@mail.ru',
            'password' => 'password'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'email' => 'test3@mail.ru',
            'phone' => '+7 (888) 456-78-91',
            'delivery_address' => 'город Москва, Ленина, 1, кв.1',
            'delivery_time' => '2025-05-05 21:00:00',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $orderItems = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);


        $adminToken = $this->postJson(route('login'), [
            'email' => 'admin@mail.ru',
            'password' => 'admin',
        ])->json('token');


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken,
        ])->patchJson((route('admin.orders.update-status', ['order' => $order->id])), [
            'status_id' => 2,
        ]);


        $response->assertStatus(200)->assertJsonStructure([
            'message',
            'data' => [
                'status',
                'delivery_address',
                'delivery_time',
                'user'
            ]
        ])->assertJson([
            'message' => 'Статус заказа изменен',
            'data' => [
                'status' => 'Доставляется',
                'delivery_address' => 'город Москва, Ленина, 1, кв.1',
                'delivery_time' => '2025-05-05 21:00',
                'user' => [
                    'name' => $user->name,
                    'email' => 'test@mail.ru',
                ]
            ]
        ]);
    }
}
