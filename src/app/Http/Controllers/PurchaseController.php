<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    // public function store(PurchaseRequest $request, $itemId)
    // {

    public function store(Request $request, $itemId)
    {

        $item = Item::findOrFail($itemId);

        // すでに売れている商品を防ぐ
        if ($item->status == 2 || $item->status === 'sold') {
            return redirect()->back()->with('error', 'この商品はすでに購入されています。');
        }

        // 購入処理（purchasesテーブルへの登録）
        Purchase::create([
            'user_id'        => Auth::id(),
            'item_id'        => $item->id,
            'payment_method' => $request->input('payment_method'),
            'postal_code'    => $request->input('postal_code'),
            'address'        => $request->input('address'),
            'building'       => $request->input('building'),

        ]);

        // 商品のbuyer_idを更新してSOLD状態にする
        $item->buyer_id = Auth::id();
        $item->status = 'sold';
        $item->save();

        // 成功後に商品一覧へリダイレクト
        return redirect()->route('items.index')->with('success', '購入が完了しました');
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