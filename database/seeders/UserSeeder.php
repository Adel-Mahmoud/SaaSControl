<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Adel Mahmoud',
            'email' => 'a@a.com',
            'phone' => '01000000000',
            'password' => Hash::make('00000000'),
            'email_verified_at' => now(),
        ]);
    }
}