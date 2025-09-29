<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ItemIndexTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    
    use RefreshDatabase;

    /** @test */
    public function 全商品を取得できる()
    {
        $items = Item::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);

        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    /** @test */
    public function 購入済み商品には_sold_ラベルが表示される()
    {
        $soldItem = Item::factory()->create([
            'status' => 'sold',
            'name'   => 'テスト商品',
        ]);

        $response = $this->get('/');

        $response->assertSee('SOLD');
        $response->assertSee($soldItem->name);
    }

    /** @test */
    public function 自分が出品した商品は表示されない()
    {
        $user = User::factory()->create();

        // 自分の商品
        Item::factory()->create([
            'user_id' => $user->id,
            'name'    => '自分の商品',
        ]);

        // 他人の商品
        $otherItem = Item::factory()->create([
            'name' => '他人の商品',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee($otherItem->name);
        $response->assertDontSee('自分の商品');
    }

    /** @test */
    public function 商品名で部分一致検索ができる()
    {
        $item1 = Item::factory()->create(['name' => 'Apple Watch']);
        $item2 = Item::factory()->create(['name' => 'Galaxy Phone']);

        $response = $this->get(route('items.index', ['keyword' => 'Apple']));

        $response->assertStatus(200);
        $response->assertSee('Apple Watch');
        $response->assertDontSee('Galaxy Phone');
    }

}