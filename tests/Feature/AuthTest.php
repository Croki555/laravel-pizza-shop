<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_successfully()
    {
        $response = $this->postJson(route('register'), [
            'name' => 'Иван Иванов',
            'email' => 'ivan2@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['token']);
    }


    /** @test */
    public function registration_fails_with_invalid_data()
    {
        $response = $this->postJson(route('register'), [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function user_can_login_successfully()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }



    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        $response = $this->postJson(route('login'), [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Неверные учетные данные']);
    }



    /** @test */
    public function authenticated_user_cannot_register()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth-token')->plainTextToken;

        $response = $this->withHeaders(([
            'Authorization' => 'Bearer ' . $token,
        ]))->postJson(route('register'), [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);


        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Действие запрещено для аутентифицированных пользователей'
        ]);
    }


    /** @test */
    public function authenticated_user_cannot_login()
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'password' => bcrypt('password')
        ]);
        $token = $user->createToken('auth-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('login'), [
            'email' => 'existing@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Действие запрещено для аутентифицированных пользователей'
        ]);
    }
}
