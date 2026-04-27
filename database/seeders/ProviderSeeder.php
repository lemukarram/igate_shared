<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'provider@igate.com'],
            [
                'name' => 'Expert Agency',
                'password' => Hash::make('password'),
                'role' => 'provider',
            ]
        );
    }
}
