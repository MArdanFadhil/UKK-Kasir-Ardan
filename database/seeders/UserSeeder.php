<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::factory()->create([
        //     'name' => 'admin',
        //     'email' => 'admin123@gmail.com',
        //     'password' => Hash::make('123'),
        //     'role' => 'admin',
        //     'remember_token' => Str::random(10),
        // ]);

        User::factory()->create([
            'name' => 'staff',
            'email' => 'staff123@gmail.com',
            'password' => Hash::make('123'),
            'role' => 'staff',
            'remember_token' => Str::random(10),
        ]);
    }
}
