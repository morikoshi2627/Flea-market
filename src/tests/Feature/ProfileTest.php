<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Item;

class ProfileTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    /** @test */
    public function プロフィールページに必要情報が表示される()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => 'default.png',
        ]);

        $sellItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品商品1',
        ]);

        $buyItem = Item::factory()->create([
            'buyer_id' => $user->id,
            'name' => '購入商品1',
        ]);

        $this->actingAs($user);

        // 出品商品タブ
        $response = $this->get(route('mypage', ['page' => 'sell']));
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee('出品商品1');
        $response->assertSee(asset('storage/item_images/' . $user->profile_image));

        // 購入商品タブ
        $response = $this->get(route('mypage', ['page' => 'buy']));
        $response->assertStatus(200);
        $response->assertSee('購入商品1');
    }

    /** @test */
    public function プロフィール編集画面に初期値が表示される()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-2-3',
            'building' => 'テストビル101',
            'profile_image' => 'default.png',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('123-4567');
        $response->assertSee('東京都渋谷区1-2-3');
        $response->assertSee('テストビル101');
        $response->assertSee(asset('storage/item_images/default.png'));
    }

    /** @test */
    public function プロフィール更新が正しく反映される()
    {
        // Storage を偽装
        Storage::fake('public');

        $user = User::factory()->create([
            'name' => '古い名前',
            'postal_code' => '000-0000',
            'address' => '古い住所',
            'building' => '古い建物',
            'profile_image' => null,
            'profile_completed' => true,
        ]);

        $this->actingAs($user);

        // GD を使わずに「画像ファイル」を作成
        $file = UploadedFile::fake()->create('profile.png', 1);

        // プロフィール更新 POST
        $response = $this->post(route('profile.update'), [
            'name' => '新しい名前',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル101',
            'profile_image' => $file,
        ]);

        // マイページへのリダイレクト確認
        $response->assertRedirect(route('mypage'));

        // DB に保存された profile_image を取得
        $user->refresh();
        $savedFileName = $user->profile_image;

        // ファイルが Storage に保存されているか確認
        Storage::disk('public')->assertExists('item_images/' . $savedFileName);

        // DB に反映されているか確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '新しい名前',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル101',
            'profile_image' => $savedFileName,
        ]);
    }
}