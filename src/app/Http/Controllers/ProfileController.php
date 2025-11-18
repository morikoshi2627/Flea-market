<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\Item;
use App\Models\Rating;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();

        $buyItems = collect();
        $sellItems = collect();
        $transactionItems = collect();

        // ▼ 取引終了（双方評価済み）の item_id を取得
        $finishedItemIds = Rating::select('item_id')
            ->groupBy('item_id')
            ->havingRaw('COUNT(*) = 2')
            ->pluck('item_id')
            ->toArray();

        // ▼ ① page に関係なく「取引中の商品」を取得（取引終了は除外）
        $allTransactionItems = Item::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->whereNotNull('buyer_id')
                ->orWhere('buyer_id', $user->id);
        })
            ->whereNotIn('id', $finishedItemIds)
            ->with('transactionMessages')
            ->get();

        // ▼ ② 全取引の未読合計数を計算（プロフィール初期表示にも使う）
        $totalUnread = 0;
        foreach ($allTransactionItems as $tItem) {
            $totalUnread += $tItem->transactionMessages()
                ->where('user_id', '!=', $user->id)
                ->where('is_read', false)
                ->count();
        }

        // ▼ ③ ページが transaction の場合のみ並び替えも含めて取得する
        if ($request->input('page') === 'transaction') {

            $transactionItems = $allTransactionItems->map(function ($item) use ($user) {
                $item->unread_count = $item->transactionMessages()
                    ->where('user_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->count();
                return $item;
            });
            // ▼ 未読順 → 更新順にソート
            $transactionItems = $transactionItems
                ->sortBy([
                    ['unread_count', 'desc'],
                    ['updated_at', 'desc'],
                ])
                ->values();

            // ▼ 合計未読件数
            $totalUnread = $transactionItems->sum('unread_count');
        }

        // ▼ sell
        if ($request->input('page') === 'sell') {
            $sellItems = Item::where('user_id', $user->id)->latest()->get();
        }

        // ▼ buy
        if ($request->input('page') === 'buy') {
            $buyItems = Item::where('buyer_id', $user->id)->latest()->get();
        }

        // ▼ 評価平均を取得
        $ratings = Rating::where('rated_id', $user->id)->pluck('score');

        if ($ratings->count() > 0) {
            $ratingAvg = round($ratings->avg());   // 四捨五入した平均値（1〜5）
        } else {
            $ratingAvg = null;  // 評価がない場合
        }

        return view('users.profile', compact(
            'user',
            'buyItems',
            'sellItems',
            'transactionItems',
            'totalUnread',
            'ratingAvg'
        ));
    }

    public function edit()
    {
        $user = auth()->user();

        // すでに登録済みなら一覧へ
        if ($user->is_profile_initialized) {
            return redirect('/');
        }

        return view('users.setting', compact('user')); // プロフィール編集画面
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'ユーザーが認証されていません');
        }

        // プロフィール画像の処理
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('item_images', 'public');
            $filename = basename($path);
            $user->profile_image = $filename;
        }

        // 他の項目
        $user->name = $request->name;
        $user->postal_code = $request->postal_code;
        $user->address = $request->address;
        $user->building = $request->building;

        // 初回プロフィール登録かどうかチェック
        $isFirstTime = ! $user->profile_completed;

        // profile_completed フラグを true にする（初回のみ）
        if ($isFirstTime) {
            $user->profile_completed = true;
        }

        $user->save();

        // 初回登録 → 商品一覧へ、それ以外はマイページへ
        if ($isFirstTime) {
            return redirect()->route('items.index'); // 商品一覧
        } else {
            return redirect()->route('mypage'); // マイページ
        }
    }
}
