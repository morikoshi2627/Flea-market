<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;

class CommentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    /** @test */
    public function ログイン済みのユーザーはコメントを送信できる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('comments.store', $item->id), [
            'comment' => 'テストコメント',
        ]);

        $response->assertRedirect(route('items.show', $item->id));
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメント',
        ]);

        $this->assertEquals(1, $item->comments()->count());
    }

    /** @test */
    public function ログイン前のユーザーはコメントを送信できない()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('comments.store', $item->id), [
            'comment' => 'テストコメント',
        ]);

        $response->assertRedirect(route('login')); // ミドルウェアによりログイン画面へ
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => 'テストコメント',
        ]);
    }

    /** @test */
    public function コメントが入力されていない場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->from(route('items.show', $item->id))
            ->post(route('comments.store', $item->id), [
                'comment' => '',
            ]);

        $response->assertRedirect(route('items.show', $item->id));
        $response->assertSessionHasErrors(['comment']);
    }

    /** @test */
    public function コメントが255字以上の場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $longComment = str_repeat('あ', 256);

        $response = $this->from(route('items.show', $item->id))
            ->post(route('comments.store', $item->id), [
                'comment' => $longComment,
            ]);

        $response->assertRedirect(route('items.show', $item->id));
        $response->assertSessionHasErrors(['comment']);
    }
}