<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create(
            [
                'name' => 'Administator',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('changeme'),
            ]
        );

        \App\Models\User::factory()->create(
            [
                'name' => 'Test User',
                'username' => 'testuser',
                'email' => 'test@example.com',
                'password' => bcrypt('testpass'),
            ]
        );
    }
}
