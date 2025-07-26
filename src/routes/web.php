<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\StripeCheckoutController;
use App\Http\Controllers\StripeWebhookController;

use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

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

// ログイン
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// 会員登録
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// メール認証画面
Route::get('/email/verify', function () {
    $user = Auth::user();

    if (!$user) {
        abort(403, 'Unauthorized');
    }

    $verifyUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    return view('auth.verify-email', compact('verifyUrl'));
})->middleware('auth')->name('verification.notice');

// メール認証処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // 認証完了

    return redirect('/mypage/profile'); // 認証完了後のリダイレクト先
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メール再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// 商品一覧・マイリスト切り替え含むトップページ
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細表示
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
// コメント投稿
Route::post('/item/{item}/comment', [CommentController::class, 'store'])->middleware(['auth', 'verified'])->name('comments.store');
// いいね/解除
Route::post('/item/{item}/favorite', [FavoriteController::class, 'toggle'])->middleware(['auth', 'verified'])->name('favorites.toggle');

// 商品購入
Route::get('/purchase/{item}', [PurchaseController::class, 'create'])->middleware(['auth', 'verified'])->name('purchase.create');
Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->middleware(['auth', 'verified'])->name('purchase.store');

// Checkout セッション作成(Stripe)
Route::post('/checkout/{item}', [StripeCheckoutController::class, 'checkout'])->name('checkout');

// 成功・キャンセル時の遷移先
Route::get('/checkout/success', [StripeCheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [StripeCheckoutController::class, 'cancel'])->name('checkout.cancel');

//  Webhook の設定（決済完了処理）
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handleWebhook']);

// 住所変更画面へ（GET）
Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])
    ->middleware(['auth', 'verified'])
    ->name('purchase.address.edit');

// 住所変更処理（POST or PUT）
Route::post('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])
    ->middleware(['auth', 'verified'])
    ->name('purchase.address.update');

// 商品出品
Route::get('/sell', [ItemController::class, 'create'])->middleware(['auth', 'verified'])->name('items.create');
Route::post('/sell', [ItemController::class, 'store'])->middleware(['auth', 'verified'])->name('items.store');

// プロフィール表示
Route::get('/mypage', [ProfileController::class, 'show'])->middleware(['auth', 'verified'])->name('mypage');

// プロフィール編集
Route::get('/mypage/profile', [ProfileController::class, 'edit'])->middleware(['auth', 'verified'])->name('profile.edit');

// プロフィール更新
Route::post('/mypage/profile', [ProfileController::class, 'update'])->middleware(['auth', 'verified'])->name('profile.update');

// プロフィール画面_購入した商品一覧（`mypage?page=buy`）
Route::get('/mypage/purchased', [PurchaseController::class, 'index'])->middleware(['auth', 'verified'])->name('mypage.purchased');

// プロフィール画面_出品した商品一覧（`mypage?page=sell`）
Route::get('/mypage/listed', [ItemController::class, 'myListings'])->middleware(['auth', 'verified'])->name('mypage.listed');