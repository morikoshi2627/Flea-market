<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Rating;
use App\Mail\TransactionCompletedMail;
use Illuminate\Support\Facades\Mail;

class RatingController extends Controller
{
    /**
     * 評価フォーム表示（モーダル）
     */
    public function create(Item $item)
    {
        $user = auth()->user();

        // 購入者だけが create() を使う
        if ($user->id !== $item->buyer_id) {
            abort(403);
        }

        // すでに評価済みなら商品一覧へ戻す
        $already = Rating::where('item_id', $item->id)
            ->where('rater_id', $user->id)
            ->exists();

        if ($already) {
            return redirect()->route('items.index');
        }

        // モーダルを表示させるためにチャットへ戻す
        return redirect()
            ->route('chat.index', ['item' => $item->id])
            ->with('show_rating_modal', true);
    }

    /**
     * 評価保存
     */
    public function store(Request $request, Item $item)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'score' => 'required|integer|min:1|max:5',
        ]);

        // 相手ユーザー
        $partner_id = ($item->user_id === $user->id)
            ? $item->buyer_id
            : $item->user_id;

        Rating::create([
            'rater_id' => $user->id,
            'rated_id' => $partner_id,
            'item_id'  => $item->id,
            'score'    => $validated['score'],
        ]);

        /**
         * 「購入者が評価したタイミング」で出品者へメール通知
         */
        if ($user->id === $item->buyer_id) {
            $seller = $item->user; // 出品者
            Mail::to($seller->email)->send(new TransactionCompletedMail($item, $user));
        }

        return redirect()->route('items.index'); // 評価後は商品一覧へ
    }
}
