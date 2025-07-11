<!-- <!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flea-market</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/index.css') }}" />

</head>

<body>
    <header class="header">
        <div class="header-inner">
            <img class="header-logo" src="{{ asset('storage/item_images/logo.svg') }}" alt="coachtechロゴ">

 
            <div class="header-search">
                <form class="search-form" action="{{ route('items.index') }}" method="GET">
                    <input class="search-input" type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？">
                    <button class="search-button" type="submit">検索</button>
                </form>
            </div>


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


    <main class="main-content"> -->

@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="main-inner">

    <div class="link-area">
        <!-- ルートは後で！！？？ -->
        <a class="recommendation-button" href="{{ route('items.index') }}">おすすめ</a>

        <!-- マイリストに遷移するリンクに検索状態を引き継がせる -->
        <a class="mylist-button" href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}">マイリスト</a>
    </div>

    <!-- 商品詳細ページへの遷移 -->
    <div class="item-list">
        @foreach ($items as $item)
        <div class="item-card">
            <a class="item-name" href="{{ route('items.show', ['item' => $item->id]) }}">
                <img class="goods-img" src="{{ asset('storage/item_images/' . $item->image) }}" alt="{{ $item->name }}">
                <p>{{ $item->name }}</p>
            </a>
        </div>
        @endforeach
    </div>



    <!-- ページネーション -->
    <div class="custom-pagination">
        {{ $items->links() }}
    </div>

</div>
@endsection
<!-- </main>
</body>

</html> -->