<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Http\Request;


class PurchaseController extends Controller
{
    public function create(Item $item)
    {
        $payment_method = request('payment_method', ''); // GETクエリで受け取る
        return view('items.purchase', compact('item', 'payment_method'));
    }

    // 支払い方法切り替え（POST）
    public function select(Request $request, Item $item)
    {
        $paymentMethod = $request->input('payment_method');
        return view('items.purchase', [
            'item' => $item,
            'payment_method' => $paymentMethod,
        ]);
    }

    // 購入処理
    public function store(PurchaseRequest $request, $itemId)
    {

        $item = Item::findOrFail($itemId);

        // 決済画面へリダイレクト（checkout セッションを作るように変更）
        return redirect()->route('checkout', $item->id)
            ->withInput($request->validated()); // 選択情報を保持したい場合

    }

    // 配送先変更画面
    public function editAddress(Item $item)
    {
        // ユーザーの現在の住所を取得
        $user = auth()->user();

        return view('addresses.edit', compact('item', 'user'));
    }

    // 配送先更新
    public function updateAddress(AddressRequest $request, Item $item)
    {

        $user = auth()->user();
        $user->postal_code = $request->postal_code;
        $user->address = $request->address;
        $user->building = $request->building;
        $user->save();

        return redirect()->route('purchase.create', ['item' => $item->id]);
    }

    // 購入履歴一覧画面での取得
    public function index()
    {
        $purchases = Purchase::where('user_id', Auth::id())->with('item')->latest()->get();
        return view('users.profile', [
            'buyItems' => $purchases->pluck('item'),
            'user'     => Auth::user(),
            'sellItems' => Auth::user()->items()->where('status', '!=', 'sold')->get(),
        ]);
    }


    public function selectPayment(Request $request, Item $item)
    {
        return redirect()->route('purchase.create', [
            'item' => $item->id,
            'payment_method' => $request->input('payment_method')
        ]);
    }

    public function show(Request $request, Item $item)
    {
        return view('items.purchase', [
            'item' => $item,
            'payment_method' => $request->input('payment_method'),
        ]);
    }
}
