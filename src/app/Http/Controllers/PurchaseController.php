<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;


class PurchaseController extends Controller
{
    public function create(Item $item)
        {

        return view('items.purchase', compact('item'));
    }

    public function store(PurchaseRequest $request, $itemId)
    {

        $item = Item::findOrFail($itemId);

        // 決済画面へリダイレクト（checkout セッションを作るように変更）
        return redirect()->route('checkout', $item->id)
            ->withInput($request->validated()); // 選択情報を保持したい場合
    
    }

    public function editAddress(Item $item)
    {
        // ユーザーの現在の住所を取得
        $user = auth()->user();

        return view('addresses.edit', compact('item', 'user'));
    }

    public function updateAddress(AddressRequest $request, Item $item)
    {

        $user = auth()->user();
        $user->postal_code = $request->postal_code;
        $user->address = $request->address;
        $user->building = $request->building;
        $user->save();

        return redirect()->route('purchase.create', $item)->with('message', '住所を更新しました');
    }

    // 購入履歴一覧画面での取得
    public function index()
    {
        $purchases = Purchase::where('user_id', Auth::id())->with('item')->latest()->get();
        return view('purchases.index', compact('purchases'));
    }
}