<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CommentController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// トップページ（商品一覧・マイリスト）
// 未ログイン状態で ?tab=mylist にアクセスされたときにログインページへリダイレクト
// Route::get('/', function () {
//     if (request()->tab === 'mylist') {
//         if (!auth()->check()) {
//             return redirect()->route('login')->with('error', 'ログインしてください');
//         }
//         return app(FavoriteController::class)->index(request());
//     }
//     return app(ItemController::class)->index(request());
// })->name('items.index');

// 商品一覧・マイリスト切り替え含むトップページ
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細表示
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
// コメント投稿
Route::post('/item/{item}/comment', [CommentController::class, 'store'])->middleware('auth')->name('comments.store');
// いいね/解除
Route::post('/item/{item}/favorite', [FavoriteController::class, 'toggle'])->middleware('auth')->name('favorites.toggle');

// 商品購入
Route::get('/purchase/{item}', [PurchaseController::class, 'create'])->middleware('auth')->name('purchase.create');
Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->middleware('auth')->name('purchase.store');

// 住所変更
Route::post('/purchase/address/{item}', [PurchaseController::class, 'update'])->middleware('auth')->name('purchase.address.update');

// 商品出品
Route::get('/sell', [ItemController::class, 'create'])->middleware('auth')->name('items.create');
Route::post('/sell', [ItemController::class, 'store'])->middleware('auth')->name('items.store');

// プロフィール表示
Route::get('/mypage', [ProfileController::class, 'show'])->middleware('auth')->name('mypage');

// プロフィール編集
Route::get('/mypage/profile', [ProfileController::class, 'edit'])->middleware('auth')->name('profile.edit');

// プロフィール更新
Route::post('/mypage/profile', [ProfileController::class, 'update'])->middleware('auth')->name('profile.update');

// プロフィール画面_購入した商品一覧（`mypage?page=buy`）
Route::get('/mypage/purchased', [PurchaseController::class, 'index'])->middleware('auth')->name('mypage.purchased');

// プロフィール画面_出品した商品一覧（`mypage?page=sell`）
Route::get('/mypage/listed', [ItemController::class, 'myListings'])->middleware('auth')->name('mypage.listed');