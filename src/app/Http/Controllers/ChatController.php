<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\TransactionMessage;
use App\Http\Requests\ChatRequest;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * チャット画面表示
     */
    public function index(Item $item)
    {
        $this->authorizeAccess($item);

        $user = auth()->user();

        // 相手ユーザー（出品者 or 購入者）
        $partner = $item->user_id === $user->id
            ? $item->buyer
            : $item->user;

        $otherTrades = Item::where(function ($q) use ($user) {
            // 自分が出品者で買い手がいる（取引が成立した商品）
            $q->where('user_id', $user->id)->whereNotNull('buyer_id')
                ->orWhere('buyer_id', $user->id);
        })
            ->orWhere(function ($q) use ($user) {
                $q->where('buyer_id', $user->id);
            })
            ->where('id', '!=', $item->id) // 今開いている取引を除外
            ->orderBy('updated_at', 'desc')
            ->get();

        // 未読状態の管理（メッセージを読むと既読になる）
        TransactionMessage::where('item_id', $item->id)
            ->where('user_id', '!=', auth()->id())  // 相手のメッセージ
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // メッセージ一覧
        $messages = $item->transactionMessages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $editId = request('edit');

        // ページ移動しても入力情報を保存（存在しなければ null）
        $draft = session("draft_message_{$item->id}");

        // ▼ 評価モーダルを表示するかどうか判定
        $show_rating_modal = false;

        $buyerRated = \App\Models\Rating::where('item_id', $item->id)
            ->where('rater_id', $item->buyer_id)
            ->exists();

        $sellerRated = \App\Models\Rating::where('item_id', $item->id)
            ->where('rater_id', $item->user_id)
            ->exists();

        // 購入者がまだ評価していない → 完了ボタンを押したら出す
        if (auth()->id() === $item->buyer_id && !$buyerRated) {
        }

        // 出品者で、購入者が評価済み & 自分は未評価
        if (auth()->id() === $item->user_id && $buyerRated && !$sellerRated) {
            session()->flash('show_rating_modal', true);
        }

        return view('chat.index', compact(
            'item',
            'messages',
            'partner',
            'otherTrades',
            'editId',
            'draft',
            'show_rating_modal'
        ));
    }

    /**
     * メッセージ投稿
     */
    public function store(ChatRequest $request, Item $item)
    {
        $this->authorizeAccess($item);

        $data = $request->validated();

        $msg = new TransactionMessage();
        $msg->item_id = $item->id;
        $msg->user_id = auth()->id();
        $msg->message = $data['message'];

        // 画像があれば保存
        if ($request->hasFile('image')) {
            $msg->image = $request->file('image')->store('chat_images', 'public');
        }

        $msg->save();

        // 送信したら下書きを消す
        session()->forget("draft_message_{$item->id}");

        return back();
    }

    /**
     * メッセージ編集
     */
    public function update(ChatRequest $request, Item $item, TransactionMessage $message)
    {
        // 自分のメッセージ以外編集不可
        if ($message->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validated();

        $message->update([
            'message' => $data['message'],
        ]);
        return redirect()->route('transaction.show', ['item' => $item->id]);
    }

    /**
     * メッセージ削除
     */
    public function destroy(Item $item, TransactionMessage $message)
    {
        if ($message->user_id !== auth()->id()) {
            abort(403);
        }

        // 画像があれば削除
        if ($message->image && Storage::disk('public')->exists($message->image)) {
            Storage::disk('public')->delete($message->image);
        }

        $message->delete();

        return back();
    }

    /**
     * アクセス権チェック（出品者 or 購入者のみ）
     */
    private function authorizeAccess(Item $item)
    {
        if (! in_array(auth()->id(), [$item->user_id, $item->buyer_id])) {
            abort(403);
        }
    }

    // draft保存
    public function saveDraft(Request $request, Item $item)
    {
        // 入力フォームの値を session に保存
        session(["draft_message_{$item->id}" => $request->input('message')]);

        // 保存後、遷移先へ飛ばす
        return redirect($request->input('redirect_to'));
    }
}
