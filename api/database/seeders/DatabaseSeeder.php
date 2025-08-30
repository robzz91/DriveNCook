<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Franchisee;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a sample franchisee
        $franchisee = Franchisee::query()->first() ?? Franchisee::create([
            'name' => 'Franchisee A',
        ]);

        // Admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
            'franchisee_id' => null,
        ]);

        // Franchisee user
        User::factory()->create([
            'name' => 'Fran User',
            'email' => 'franchisee@example.com',
            'password' => 'password',
            'role' => 'franchisee',
            'franchisee_id' => $franchisee->id,
        ]);
    }
}
