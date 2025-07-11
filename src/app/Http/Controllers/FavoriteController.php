<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = $user->favorites()->with('item'); // お気に入り商品一覧

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->whereHas('item', function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%');
            });
        }

        $items = $query->latest()->paginate(10)->appends($request->all());

        return view('items.index', compact('items'));
    }

    // いいね切り替え機能
    public function toggle($itemId)
    {
        $user = Auth::user();
        $item = Item::findOrFail($itemId);

        // すでに「いいね」しているかチェック
        if ($user->favorites()->where('item_id', $item->id)->exists()) {
            // いいねを外す
            $user->favorites()->detach($item->id);
        } else {
            // いいねを追加
            $user->favorites()->attach($item->id);
        }

        return redirect()->back();
    }

}
