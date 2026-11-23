<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@newsapp.test'],
            ['name' => 'Quản trị viên', 'password' => Hash::make('12345678'), 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'user@newsapp.test'],
            ['name' => 'Độc giả mẫu', 'password' => Hash::make('12345678'), 'role' => 'user']
        );
    }
}
