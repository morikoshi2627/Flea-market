<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function (Request $request) {
            return view('auth.register');
                 });

        // ログイン画面のビュー設定
        Fortify::loginView(function (Request $request) {
                     return view('auth.login');
                 });

        // フォームリクエストを用いた認証ロジック
        Fortify::authenticateUsing(function ($request) {
            $formRequest = app(\App\Http\Requests\LoginRequest::class);
            $formRequest->merge($request->only(['email', 'password']));
            $formRequest->validateResolved();

            // 認証処理
            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                return $user;
            }

            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => ['ログイン情報が登録されていません'],
            ]);
        });

        // レートリミット（ログイン試行制限）
        RateLimiter::for('login', function (Request $request) {
                     $email = (string) $request->email;
            
                     return Limit::perMinute(10)->by($email . $request->ip());
                 });
            
    }
}