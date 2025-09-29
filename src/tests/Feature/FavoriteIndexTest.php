<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;


class FavoriteIndexTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    
    use RefreshDatabase;

    /** @test */
    public function いいねした商品だけが表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 他人の商品を2つ作成
        $item1 = Item::factory()->create(['name' => 'テスト商品A']);
        $item2 = Item::factory()->create(['name' => 'テスト商品B']);

        // item1をお気に入りに追加
        $user->favorites()->attach($item1->id);

        // マイリストページにアクセス
        $response = $this->get(route('items.index', ['tab' => 'mylist']));

        $response->assertStatus(200);
        $response->assertSee($item1->name);
        $response->assertDontSeeText($item2->name); // いいねしてない商品は出ない
    }

    /** @test */
    public function 購入済み商品には_sold_ラベルが表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $soldItem = Item::factory()->create([
            'status' => 'sold',
        ]);

        $user->favorites()->attach($soldItem->id);

        $response = $this->get(route('items.index', ['tab' => 'mylist']));

        $response->assertStatus(200);
        $response->assertSee('SOLD');
    }

    /** @test */
    public function 未認証の場合は何も表示されない()
    {
        $item = Item::factory()->create(['name' => 'テスト商品D']);

        $response = $this->get(route('items.index', ['tab' => 'mylist']));

        $response->assertStatus(200);

        // ログインしていないので何も表示されない（アイテム名が出ないことを確認）
        $response->assertDontSeeText($item->name);
    }

    /** @test */
    public function 検索状態がマイリストでも保持されている()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item1 = Item::factory()->create(['name' => 'Nike Shoes']);
        $item2 = Item::factory()->create(['name' => 'Adidas Jacket']);

        // Nikeだけをいいね
        $user->favorites()->attach($item1->id);
        $user->favorites()->attach($item2->id);

        // 検索キーワード付きでマイリストにアクセス
        $response = $this->get(route('items.index', [
            'tab' => 'mylist',
            'keyword' => 'Nike'
        ]));

        $response->assertStatus(200);
        $response->assertSee('Nike Shoes');
        $response->assertDontSee('Adidas Jacket');

        // bladeの <input value="{{ request('keyword') }}"> にも反映されることを確認
        $response->assertSee('value="Nike"', false);
    }

    /** @test */
    public function いいねアイコンを押すと商品がお気に入りに登録される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create();

        // 商品詳細ページでいいね押下（POSTリクエスト）
        $response = $this->post(route('favorites.toggle', $item->id));

        $response->assertRedirect(); // リダイレクトされる
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function いいね済みの商品は赤いアイコンが表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create();
        $user->favorites()->attach($item->id);

        $response = $this->get(route('items.show', $item->id));

        $response->assertStatus(200);
        // star-red.png が表示されることを確認
        $response->assertSee('star-red.png');
    }

    /** @test */
    public function いいねしていない商品は灰色アイコンが表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create();

        $response = $this->get(route('items.show', $item->id));

        $response->assertStatus(200);
        // star.png が表示されることを確認
        $response->assertSee('star.png');
        $response->assertDontSee('star-red.png');
    }

    /** @test */
    public function 再度いいねアイコンを押すと解除される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create();
        $user->favorites()->attach($item->id);

        // 再度押下で解除
        $response = $this->post(route('favorites.toggle', $item->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

}