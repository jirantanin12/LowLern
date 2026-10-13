<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // 1. บัญชี Admin (พี่)
        User::create([
            'name' => 'Admin (พี่)',
            'email' => 'admin@lowlern.com',
            'password' => Hash::make('admin1234'), // รหัสผ่าน Admin
            'role' => 'ADMIN',
        ]);

        // 2. บัญชี น้อง (Sibling)
        User::create([
            'name' => 'น้อง',
            'email' => 'sibling@lowlern.com',
            'password' => Hash::make('sibling1234'), // รหัสผ่านที่แอดมินตั้งให้น้อง
            'role' => 'SIBLING',
        ]);
        
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
