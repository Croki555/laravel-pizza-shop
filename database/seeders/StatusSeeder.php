<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('statuses')->insertOrIgnore([
            [
                'name' => 'В работе',
            ],
            [
                'name' => 'Доставляется',
            ],
        ]);
    }
}
