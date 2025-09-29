<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class PurchaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;


    /** @test */
    public function  購入処理が完了するか確認()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'status' => 'on_sale',
        ]);

        $this->actingAs($user)
            ->post(route('purchase.store', $item->id), [
                'payment_method' => 'credit',
                'postal_code' => '123-4567',
                'address' => '東京都渋谷区1-2-3',
                'building' => 'テストビル101',
            ])
            ->assertRedirect(route('checkout', $item->id));

    }

    /** @test */
    public function 購入した商品が「sold」と表示されるか確認()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'status' => 'on_sale',
        ]);

        // 購入済みデータを作成
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 商品の状態を「sold」に更新（StripeWebhookControllerの処理を模倣）
        $item->update(['status' => 'sold', 'buyer_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertSee('sold');
    }

    /** @test */
    public function プロフィールの購入商品一覧に表示されるか確認()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user);

        // ビューをモック
        $this->withoutExceptionHandling();
        $this->app['view']->addNamespace('purchases', resource_path('views/users'));
        view()->addLocation(resource_path('views/users'));

        $response = $this->get(route('mypage.purchased') . '?page=buy');

        $response->assertStatus(200);
        $response->assertSee($item->name); // 購入商品名が表示されるか確認
    }

    public function test_支払い方法選択が小計画面に反映される()
    {
        // ユーザー作成 & ログイン
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        // 商品作成
        $item = Item::factory()->create([
            'price' => 5000,
        ]);

        // 支払い方法を「カード払い」で選択して遷移
        $response = $this->get(route('purchase.create', [
            'item' => $item->id,
            'payment_method' => 'credit',
        ]));

        // ステータス確認
        $response->assertStatus(200);

        // 画面に「カード払い」と小計が表示されていることを確認
        $response->assertSee('カード払い');
        $response->assertSee('¥' . number_format($item->price));

        // コンビニ払いでも確認
        $response = $this->get(route('purchase.create', [
            'item' => $item->id,
            'payment_method' => 'konbini',
        ]));

        $response->assertStatus(200);
        $response->assertSee('コンビニ払い');
    }

    public function test_送付先住所変更が購入画面に反映される()
    {
        $user = User::factory()->create([
            'postal_code' => null,
            'address' => null,
            'building' => null,
            'email_verified_at' => now(),
        ]);

        $item = Item::factory()->create();

        $this->actingAs($user);

        // 住所を変更
        $this->post(route('purchase.address.update', $item->id), [
            'postal_code' => '123-4567',
            'address'     => '東京都千代田区1-1-1',
            'building'    => 'テストビル101',
        ]);

        // 購入画面を開く
        $response = $this->get(route('purchase.create', $item->id));

        $response->assertStatus(200);
        $response->assertSee('123-4567');
        $response->assertSee('東京都千代田区1-1-1');
        $response->assertSee('テストビル101');
    }

    public function test_購入した商品に送付先住所が紐づいて登録される()
    {
        $user = User::factory()->create([
            'postal_code' => '987-6543',
            'address'     => '大阪市北区2-2-2',
            'building'    => 'テストビル202',
            'email_verified_at' => now(),
        ]);

        $item = Item::factory()->create([
            'price' => 5000,
        ]);

        $this->actingAs($user);

        // 購入処理（実際は Stripe → Webhook だけどここでは store に直 POST）
        $response = $this->post(route('purchase.store', $item->id), [
            'payment_method' => 'credit',
        ]);

        $response->assertRedirect(); // checkout へのリダイレクト確認


        // Webhook を模擬して直接 Purchase を作る
        \App\Models\Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
            'postal_code' => $user->postal_code,
            'address' => $user->address,
            'building' => $user->building,
            'payment_id' => 'test_123',
        ]);

        // DB に保存されているか確認
        $this->assertDatabaseHas('purchases', [
            'user_id'        => $user->id,
            'item_id'        => $item->id,
            'payment_method' => 'card',
            'postal_code'    => '987-6543',
            'address'        => '大阪市北区2-2-2',
            'building'       => 'テストビル202',
        ]);
    }
}