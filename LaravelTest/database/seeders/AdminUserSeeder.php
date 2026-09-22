<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@admin.ch'],
            ['name' => 'Admin', 'password' => 'Pqrs-tils8', 'role' => 'admin']
        );
    }
}