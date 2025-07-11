<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ItemCategory;

class ItemController extends Controller
{
    // 商品一覧画面（トップ）
    public function index(Request $request)
    {
        $query = Item::query();

        // 商品名で部分一致検索
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        // 「マイリスト」タブ（いいね一覧）
        if ($request->input('tab') === 'mylist') {
            if (!auth()->check()) {
                return redirect()->route('login')->with('error', 'ログインしてください');
            }

            $user = auth()->user();
            $query->whereHas('favorites', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        $items = $query->latest()->paginate(10)->appends($request->all());

        return view('items.index', compact('items'));
    }

    // 商品詳細画面
    public function show($id)
    {
        $item = Item::with(['user', 'brand', 'condition', 'categories', 'favorites', 'comments.user'])
            ->findOrFail($id);

        // ログイン中のユーザーがこの商品を「いいね」済みかどうか
        $isFavorited = auth()->check()
            ? $item->favorites()->where('user_id', auth()->id())->exists()
            : false;

        return view('items.show', compact('item', 'isFavorited'));
    }

    // 出品処理
    public function create()
    {
        $categories = \App\Models\Category::all();
        $conditions = \App\Models\Condition::all();
        return view('items.sell', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $user = Auth::user();

        // 商品画像保存
        $path = $request->file('image')->store('public/item_images');
        $filename = basename($path);

        // 商品保存
        $item = Item::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $filename,
            'brand_id' => $request->brand_id,
            'condition_id' => $request->condition_id,
            'price' => $request->price,
        ]);

        // カテゴリ中間テーブルに登録（複数対応）
        foreach ($request->categories as $categoryId) {
            ItemCategory::create([
                'item_id' => $item->id,
                'category_id' => $categoryId,
            ]);
        }

        return redirect()->route('items.index')->with('success', '商品を出品しました');
    }
}