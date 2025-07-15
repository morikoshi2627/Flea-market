<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\Purchase;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();

        $buyItems = collect();
        $sellItems = collect();

        // ?page=buy の場合は購入履歴を取得
        if ($request->input('page') === 'buy') {
            $buyItems = \App\Models\Purchase::where('user_id', $user->id)->with('item')->latest()->get();
        }

        // ?page=sell の場合は出品商品を取得
        if ($request->input('page') === 'sell') {
            $sellItems = \App\Models\Item::where('user_id', $user->id)->latest()->get();
        }

        return view('users.profile', compact('user', 'buyItems', 'sellItems'));
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
            $path = $request->file('profile_image')->store('public/item_images');
            $filename = basename($path); // ← ファイル名だけ取り出す
            $user->profile_image = $filename; // ← ファイル名のみをDBに保存
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
