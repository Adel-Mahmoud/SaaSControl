<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Adel Mahmoud',
            'email' => 'a@a.com',
            'password' => Hash::make('00000000'),
            'email_verified_at' => now(),
        ]);
    }
}