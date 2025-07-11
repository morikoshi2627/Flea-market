<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Brand;
use App\Models\Condition;
use App\Models\User;
use App\Models\Category;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['name' => '腕時計', 'price' => 15000, 'brand' => 'Rolax',     'condition' => '良好', 'description' => 'スタイリッシュなデザインのメンズ腕時計',     'image' => 'Armani+Mens+Clock.jpg'],
            ['name' => 'HDD',   'price' => 5000,  'brand' => '西芝',      'condition' => '目立った傷や汚れなし', 'description' => '高速で信頼性の高いハードディスク',     'image' => 'HDD+Hard+Disk.jpg'],
            ['name' => '玉ねぎ3束', 'price' => 300,   'brand' => 'なし',       'condition' => 'やや傷や汚れあり', 'description' => '新鮮な玉ねぎ3束のセット',     'image' => 'iLoveIMG+d.jpg'],
            ['name' => '革靴', 'price' => 4000,  'brand' => null,       'condition' => '状態が悪い', 'description' => 'クラシックなデザインの革靴',     'image' => 'Leather+Shoes+Product+Photo.jpg'],
            ['name' => 'ノートPC', 'price' => 45000, 'brand' => null,       'condition' => '良好', 'description' => '高性能なノートパソコン',     'image' => 'Living+Room+Laptop.jpg'],
            ['name' => 'マイク', 'price' => 8000,  'brand' => 'なし',       'condition' => '目立った傷や汚れなし', 'description' => '高音質のレコーディング用マイク',     'image' => 'Music+Mic+4632231.jpg'],
            ['name' => 'ショルダーバッグ', 'price' => 3500,  'brand' => null,       'condition' => 'やや傷や汚れあり', 'description' => 'おしゃれなショルダーバッグ',     'image' => 'Purse+fashion+pocket.jpg'],
            ['name' => 'タンブラー', 'price' => 500,   'brand' => 'なし',       'condition' => '状態が悪い', 'description' => '使いやすいタンブラー',     'image' => 'Tumbler+souvenir.jpg'],
            ['name' => 'コーヒーミル', 'price' => 4000,  'brand' => 'Starbacks', 'condition' => '良好', 'description' => '手動のコーヒーミル',     'image' => 'Waitress+with+Coffee+Grinder.jpg'],
            ['name' => 'メイクセット', 'price' => 2500,  'brand' => null,       'condition' => '目立った傷や汚れなし', 'description' => '便利なメイクアップセット',     'image' => 'makeup-set.jpg'],
        ];

        // 出品次郎のIDを取得
        $userId = User::where('email', 'seller2@example.com')->value('id');

        foreach ($items as $item) {
            $newItem = Item::create([
                'name' => $item['name'],
                'price' => $item['price'],
                'brand_id' => $item['brand'] ? Brand::where('name', $item['brand'])->value('id') : null,
                'condition_id' => Condition::where('name', $item['condition'])->value('id'),
                'description' => $item['description'],
                'image' => $item['image'],
                'user_id' => $userId,
            ]);
            // カテゴリをランダムで1〜3個付ける
            $categoryIds = Category::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $newItem->categories()->attach($categoryIds);
        }
    }
}
