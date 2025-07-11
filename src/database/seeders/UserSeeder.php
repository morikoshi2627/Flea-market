<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 出品太郎
        User::firstOrCreate([
            'name' => '出品太郎',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'), // ログイン用
            'profile_image' => 'jurica-koletic-7YVZYZeITc8-unsplash.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都千代田区1-1-1',
            'building' => 'フリマビル101',
        ]);

        // 出品次郎
        User::firstOrCreate(
            ['email' => 'seller2@example.com'],
            [
                'name' => '出品次郎',
                'password' => Hash::make('password456'),
                'profile_image' => 'card3.jpg',
                'postal_code' => '987-6543',
                'address' => '大阪府大阪市2-2-2',
                'building' => 'マーケットビル202',
            ]
        );
    }
}