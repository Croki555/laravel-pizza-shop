<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(5)->create();

        User::factory()
            ->create([
                'name' => 'Test',
                'email'=> 'test@mail.ru',
                'password' => 'password'
            ]);

        User::factory()->admin()
            ->create([
                'name' => 'Admin',
                'email'=> 'admin@mail.ru',
                'password' => 'admin'
            ]);

        $this->call([
            StatusSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
