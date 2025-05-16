<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
            User::factory()->create([
                'name' => 'Rina Noviana',
                'email' => 'Rina@gmail.com',
                'password' => bcrypt('secret'),
                'phone_number' => '6283816002326',
                'role' => 'ceo',
            ]);

            User::factory()->create([
            'name' => 'Mona',
            'email' => 'Mona12@gmail.com',
            'password' => bcrypt('User.123'),
            'phone_number' => '6281223203017',
                'role' => 'member',
            ]);
        }
    }
