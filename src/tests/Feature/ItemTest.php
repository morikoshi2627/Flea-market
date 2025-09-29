<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Brand;

class ItemTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    /** @test */
    public function 商品出品が正しく保存される()
    {
        // Storageを偽装
        Storage::fake('public');

        // テスト用ユーザー作成
        $user = User::factory()->create();

        // テスト用カテゴリ・ブランド・状態作成
        $category = Category::factory()->create(['name' => '家具']);
        $condition = Condition::factory()->create(['name' => '新品']);
        $brand = Brand::factory()->create(['name' => 'テストブランド']);

        $this->actingAs($user);

        // GD を使わずにダミーファイルを作成
        $image = UploadedFile::fake()->create('item.png', 100);

        // 商品出品POST
        $response = $this->post(route('items.store'), [
            'name' => 'テスト商品',
            'description' => 'テスト商品の説明です',
            'price' => 1000,
            'brand_id' => $brand->id,
            'condition_id' => $condition->id,
            'categories' => [$category->id],
            'image' => $image,
        ]);

        // リダイレクト確認
        $response->assertRedirect();

        // 保存された商品を取得
        $item = Item::first();

        // DBに正しく保存されているか確認（itemsテーブル）
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'description' => 'テスト商品の説明です',
            'price' => 1000,
            'brand_id' => $brand->id,
            'condition_id' => $condition->id,
        ]);

        // 中間テーブルに正しく保存されているか確認
        $this->assertDatabaseHas('item_categories', [
            'item_id' => $item->id,
            'category_id' => $category->id,
        ]);

        // Storageに画像が保存されているか確認
        Storage::disk('public')->assertExists('item_images/' . $item->image);
    }
}