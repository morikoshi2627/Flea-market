<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Comment;
use App\Models\User;
use App\Models\Item;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),   // コメントしたユーザー
            'item_id' => Item::factory(),   // コメント対象の商品
            'content' => $this->faker->sentence(), // コメント内容
        ];
    }
}