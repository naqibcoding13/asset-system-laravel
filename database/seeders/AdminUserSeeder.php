<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@spa.local',
                'unit' => 'Bahagian Khidmat Pengurusan',
                'role' => 'admin',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
