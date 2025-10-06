<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'first_name' => 'robotic',
            'last_name' => 'club', 
            'email' => 'robotic-club@gmail.com',
            'password' => Hash::make('password'), 
            'phone' => '0000000000', 
            'role' => 'Admin',     
        ]);
        
        
    }
}
