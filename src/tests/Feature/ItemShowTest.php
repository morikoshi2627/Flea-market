<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Comment;


class ItemShowTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    /** @test */
    public function 商品詳細ページに必要な情報が表示される()
    {
        $user = User::factory()->create();
        $brand = Brand::factory()->create(['name' => 'NIKE']);
        $condition = Condition::factory()->create(['name' => '新品']);
        $categories = Category::factory()->count(2)->create();

        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'description' => '商品の説明文です',
            'brand_id' => $brand->id,
            'condition_id' => $condition->id,
            'price' => 12000,
            'status' => 'sold',
        ]);

        // カテゴリ紐付け
        $item->categories()->attach($categories->pluck('id'));

        // コメント追加
        $commentUser = User::factory()->create(['name' => 'コメントユーザー']);
        Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $commentUser->id,
            'content' => 'とてもいい商品ですね！',
        ]);

        $response = $this->get(route('items.show', $item->id));

        $response->assertStatus(200);

        // 商品基本情報
        $response->assertSee('テスト商品')
            ->assertSee('NIKE')
            ->assertSee('12,000')
            ->assertSee('SOLD')
            ->assertSee('商品の説明文です');

        // カテゴリ
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }

        // 状態
        $response->assertSee('新品');

        // コメント
        $response->assertSee('コメントユーザー')
            ->assertSee('とてもいい商品ですね！');
    }

    /** @test */
    public function 複数カテゴリが表示される()
    {
        $item = Item::factory()->create();
        $categories = Category::factory()->count(3)->create();
        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get(route('items.show', $item->id));

        $response->assertStatus(200);

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}