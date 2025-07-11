@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
<div class="main-inner">

    <!-- 商品画像 -->
    <img class="item-image" src="{{ asset('storage/item_images/' . $item->image) }}" alt="{{ $item->name }}">

    <div class="show-content">
        <!--  商品名・ブランド名・価格 -->
        <h2 class="item-name">{{ $item->name }}</h2>
        <div class="brand">
            <p class="brand-title">ブランド名</p>
            <p class="brand-name"> {{ $item->brand->name ?? 'なし' }}</p>
        </div>

        <div class="price-area">
            <p class="price-mark">¥</p>
            <p class="price">{{ number_format($item->price) }} </p>
            <p class="price-tax">（税込）</p>
        </div>

        <div class="icon-stats">
            <!-- いいねアイコンと数 -->
            @auth
            <form method="POST" action="{{ route('favorites.toggle', ['item' => $item->id]) }}">
                @csrf
                <button class="icon-button" type="submit">
                    <img class="icon-img" src="{{ asset('storage/item_images/' . (auth()->user()->favorites->contains($item->id) ? 'star-red.png' : 'star.png')) }}" alt="いいね">
                    <span class="icon-button-span">{{ $item->favorites->count() }}</span>
                </button>
            </form>

            @else
            <div class="icon-button">
                <img class="icon-img" src="{{ asset('storage/item_images/star.png') }}?v={{ time() }}" alt="いいね">
                <span class="icon-button-span">{{ $item->favorites->count() }}</span>
            </div>
            <p><a href="{{ route('login') }}">ログイン</a>すると「いいね」できます。</p>
            @endauth

            <!-- コメントアイコンと数 -->
            <div class="icon-button">
                <img class="icon-img" src="{{ asset('storage/item_images/comment.png') }}" alt="コメント">
                <span class="icon-img-span">{{ $item->comments->count() }}</span>
            </div>
        </div>

        <!-- 購入ボタン　-->
        <a class="button-submit" href="{{ route('purchase.create', $item->id) }}">購入手続きへ</a>

        <!-- 商品説明 -->
        <h3 class="item-version-title">商品説明</h3>
        <p class="item-version">{{ $item->description }}</p>

        <!-- 商品情報 -->
        <h3 class="item-version-title">商品の情報</h3>
        <div class="item-1">
            <p class="item-version2">商品の状態</p>
            <p class="item-information">{{ $item->condition->name }}</p>
        </div>
        <div class="item-2">
            <p class="item-version2">カテゴリー</p>
            <div class="category-tags">
                @foreach ($item->categories as $category)
                <span class="item-information2">
                    {{ $category->name }}
                </span>
                @endforeach
            </div>
        </div>

        <!-- コメント一覧表示 -->
        <h3 class="comment-title">コメント（{{ $item->comments->count() }}件）</h3>
        @if ($item->comments->isEmpty())
        <p>コメントはまだありません。</p>
        @else
        <ul>
            @foreach ($item->comments as $comment)
            <li>
                <strong>{{ $comment->user->name }}さん：</strong>
                {{ $comment->content }}
                <br>
                <small>{{ $comment->created_at->format('Y年m月d日 H:i') }}</small>
            </li>
            @endforeach
        </ul>
        @endif

        <!-- コメント投稿フォーム -->
         <h4 class="comment-form">商品へのコメント</h4>
        @if (Auth::check())
        <form action=" {{ route('comments.store', $item->id) }}" method="POST">
            @csrf
            <textarea class="comment-area" name="comment" rows="3">{{ old('comment') }}</textarea>
            @error('comment')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror

            <button class="button-submit" type="submit">コメントを送信する</button>
            </form>
            @else
            <p><a href="{{ route('login') }}">ログイン</a>するとコメントを投稿できます。</p>
            @endif
    </div>
</div>
@endsection