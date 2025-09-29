<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemCategory;
use Illuminate\Pagination\LengthAwarePaginator;

class ItemController extends Controller
{

    public function index(Request $request)
    {
        $tab = $request->input('tab');
        $keyword = $request->input('keyword');

        // デフォルト: ログインしていれば出品除外
        $itemQuery = Item::query();

        if (Auth::check()) {
            $itemQuery->where('user_id', '!=', Auth::id());
        }

        if ($keyword) {
            $itemQuery->where('name', 'like', '%' . $keyword . '%');
        }

        if ($tab === 'mylist') {
            if (!Auth::check()) {
                // 空のPaginatorを用意
                $items = new LengthAwarePaginator([], 0, 10, 1, [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]);
            } else {
                $user = Auth::user();

                $favoriteQuery = $user->favorites();
                if (!empty($keyword)) {
                    $favoriteQuery->where('name', 'like', '%' . $keyword . '%');
                }

                $items = $favoriteQuery->latest()->paginate(10)->appends($request->all());
            }
        } else {
            $items = $itemQuery->latest()->paginate(10)->appends($request->all());
        }
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

        if ($request->hasFile('image')) { // ファイルがあるかチェック
            $path = $request->file('image')->store('item_images', 'public'); // publicディスクに保存
            $imageName = basename($path); // ファイル名だけ取り出す
        } else {
            $imageName = null; // ファイルがない場合は null を設定
        }

        // 商品保存
        $item = Item::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imageName,
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

        return redirect()->route('items.index');
    }
}