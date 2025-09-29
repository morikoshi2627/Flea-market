<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\User;
use App\Models\Brand;
use App\Models\Condition;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'user_id'       => User::factory(), // 出品者
            'buyer_id'      => null, // 未購入のためnull
            'brand_id'      => Brand::factory(), // ブランド（nullableなのでnullでもOK）
            'condition_id'  => Condition::factory(), // 商品状態
            'name'          => $this->faker->word(), // 商品名
            'price'         => $this->faker->numberBetween(500, 5000), // 価格
            'description'   => $this->faker->sentence(), // 商品説明
            'image'         => 'sample.jpg', // ダミー画像
            'status'        => 'selling', // selling または sold
        ];
    }

    /**
     * 売り切れ状態のアイテムを作るステート
     */
    public function sold(): Factory
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'sold',
            'buyer_id' => User::factory(), // 購入者を自動生成
        ]);
    }
}
