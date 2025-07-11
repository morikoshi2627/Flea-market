<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flea-market</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header-inner">
            <img class="header-logo" src="{{ asset('storage/item_images/logo.svg') }}" alt="coachtechロゴ">

            <!-- 中央: 商品検索 -->
            <div class="header-search">
                <form class="search-form" action="{{ route('items.index') }}" method="GET">
                    <input class="search-input" type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？">
                    <button class="search-button" type="submit">検索</button>
                </form>
            </div>

            <!-- 各種ボタン -->
            <div class="header-right">
                <form class="form-actions" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-button" type="submit">ログアウト</button>
                </form>
                <a class="mypage-button" href="{{ route('mypage') }}">マイページ</a>
                <a class="listing-button" href="{{ route('items.create') }}">出品</a>
            </div>
        </div>
    </header>

    <main class="main-content">
        @yield('content')
    </main>
</body>

</html>