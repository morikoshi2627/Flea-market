<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/mypage/profile';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('web')
                // ->namespace($this->namespace) // ここを削除するか null でないことを確認
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                // ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }

    // 未認証ユーザー向けのリダイレクト設定＋プロフィール設定
    protected function redirectTo(Request $request)
    {
        $user = $request->user();

        // 認証ユーザーが存在しない or メール未認証ならメール認証ページへ
        if (! $user || ! $user->hasVerifiedEmail()) {
            return route('verification.notice');
        }

        // プロフィールが未設定ならプロフィール編集画面へ
        if (! $user->profile_completed) {
            return route('profile.edit');
        }

        // すべて完了していれば商品一覧へ
        return route('items.index');
    }
}
