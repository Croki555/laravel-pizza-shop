<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->pushMiddleware(\Illuminate\Session\Middleware\StartSession::class);
    }

    protected function createPizzaCategory(): Category
    {
        return Category::factory()->create(['id' => 1, 'name' => 'Пицца']);
    }

    protected function createDrinkCategory(): Category
    {
        return Category::factory()->create(['id' => 2, 'name' => 'Напиток']);
    }

    protected function createPepperoniPizza(Category $category): Product
    {
        return Product::factory()->create([
            'name' => 'Пепперони',
            'description' => 'Классическая пицца с пепперони',
            'category_id' => $category->id,
            'price' => 550.00
        ]);
    }

    protected function createFantaDrink(Category $category): Product
    {
        return Product::factory()->create([
            'name' => 'Фанта',
            'description' => 'Апельсиновый газированный напиток',
            'category_id' => $category->id,
            'price' => 550.00
        ]);
    }

    protected function addToCart($productId, $quantity = 1)
    {
        return $this->postJson(route('cart.add'), [
            'product_id' => $productId,
            'quantity' => $quantity
        ]);
    }

    /** @test */
    public function user_can_see_cart()
    {
        $response = $this->getJson(route('cart.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'total_items',
                    'total_price',
                    'cart_items'
                ]
            ])
            ->assertJson([
                'message' => 'Ваша корзина',
                'data' => [
                    'total_items' => 0,
                    'total_price' => 0,
                    'cart_items' => []
                ]
            ]);
    }

    /** @test */
    public function user_can_see_cart_with_items()
    {
        $category = $this->createPizzaCategory();
        $pepperoni = $this->createPepperoniPizza($category);

        $this->addToCart($pepperoni->id, 2)->assertStatus(200);

        $response = $this->getJson(route('cart.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'total_items',
                    'total_price',
                    'cart_items' => [
                        '*' => [
                            'product_id',
                            'quantity',
                            'name',
                            'price',
                            'category'
                        ]
                    ]
                ]
            ])
            ->assertJson([
                "message" => "Ваша корзина",
                'data' => [
                    'total_items' => 2,
                    'total_price' => 1100.00,
                    'cart_items' => [
                        [
                            'product_id' => $pepperoni->id,
                            'quantity' => 2,
                            'name' => 'Пепперони',
                            'price' => 550.00,
                            "category" => "Пицца"
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function user_can_added_product_to_cart()
    {
        $category = $this->createPizzaCategory();
        $pepperoni = $this->createPepperoniPizza($category);

        $response = $this->addToCart($pepperoni->id, 2);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'total_items',
                    'total_price',
                    'cart_items' => [
                        '*' => [
                            'product_id',
                            'quantity',
                            'name',
                            'price',
                            'category'
                        ]
                    ]
                ]
            ])
            ->assertJson([
                "message" => "Товар успешно добавлен в корзину",
                'data' => [
                    'total_items' => 2,
                    'total_price' => 1100.00,
                    'cart_items' => [
                        [
                            'product_id' => $pepperoni->id,
                            'quantity' => 2,
                            'name' => 'Пепперони',
                            'price' => 550.00,
                            "category" => "Пицца"
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function user_cannot_add_nonexistent_product_to_cart()
    {
        $category = $this->createPizzaCategory();
        $pepperoni = $this->createPepperoniPizza($category);

        $response = $this->addToCart(9999, 1);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJson([
                'message' => 'Значение поля product id не существует.',
                'errors' => [
                    'product_id' => ['Значение поля product id не существует.'],
                ]
            ]);
    }


    /** @test */
    public function user_cannot_add_more_than_10_pizzas()
    {
        $category = $this->createPizzaCategory();
        $pepperoni = $this->createPepperoniPizza($category);


        $response = $this->postJson(route('cart.add'), [
            'product_id' => $pepperoni->id,
            'quantity' => 11
        ]);


        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJson([
                'message' => 'Максимум 10 пицц. Текущее количество: 0',
                'errors' => [
                    'product_id' => [
                        'Максимум 10 пицц. Текущее количество: 0'
                    ]
                ]
            ]);
    }


    /** @test */
    public function user_cannot_add_more_than_20_drinks()
    {
        $categoryPizza = $this->createPizzaCategory();
        $categoryDrink = $this->createDrinkCategory();
        $fanta = $this->createFantaDrink($categoryDrink);


        $response = $this->postJson(route('cart.add'), [
            'product_id' => $fanta->id,
            'quantity' => 21
        ]);


        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJson([
                'message' => 'Максимум 20 напитков. Текущее количество: 0',
                'errors' => [
                    'product_id' => [
                        'Максимум 20 напитков. Текущее количество: 0'
                    ]
                ]
            ]);
    }

    /** @test */
    public function user_can_remove_product_to_quantity()
    {
        $category = $this->createPizzaCategory();
        $pepperoni = $this->createPepperoniPizza($category);

        $this->addToCart($pepperoni->id, 4);

        $response = $this->deleteJson(route('cart.remove'), [
            'product_id' => $pepperoni->id,
            'quantity' => 2
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'total_items',
                    'total_price',
                    'cart_items' => [
                        '*' => [
                            'product_id',
                            'quantity',
                            'name',
                            'price',
                            'category'
                        ]
                    ]
                ]
            ])
            ->assertJson([
                "message" => "Количество товара уменьшено",
                'data' => [
                    'total_items' => 2,
                    'total_price' => 1100.00,
                    'cart_items' => [
                        [
                            'product_id' => $pepperoni->id,
                            'quantity' => 2,
                            'name' => 'Пепперони',
                            'price' => 550.00,
                            "category" => "Пицца"
                        ]
                    ]
                ]
            ]);
    }



    /** @test */
    public function user_can_clear_cart()
    {
        $categoryPizza = $this->createPizzaCategory();
        $categoryDrink = $this->createDrinkCategory();
        $pepperoni = $this->createPepperoniPizza($categoryPizza);
        $fanta = $this->createFantaDrink($categoryDrink);

        $this->addToCart($pepperoni->id, 2)->assertStatus(200);
        $this->addToCart($fanta->id, 5)->assertStatus(200);

        $response = $this->deleteJson(route('cart.clear'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'total_items',
                    'total_price',
                    'cart_items'
                ]
            ])
            ->assertJson([
                "message" => "Корзина успешно очищена",
                'data' => [
                    'total_items' => 0,
                    'total_price' => 0,
                    'cart_items' => []
                ]
            ]);
    }
}
