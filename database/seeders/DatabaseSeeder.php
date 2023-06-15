<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(10)->create();
        \App\Models\User::create([
            'name' => 'yonetici',
            'email' => 'yonetici@example.com',
            'password' => Hash::make('123123'),
            'role_id' => 1
        ]);
        \App\Models\User::create([
            'name' => 'kullanici',
            'email' => 'kullanici@example.com',
            'password' => Hash::make('123123'),
            'role_id' => 2
        ]);
    }
}
