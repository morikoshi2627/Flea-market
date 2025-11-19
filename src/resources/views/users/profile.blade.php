@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="main-inner">

    <div class="profile-inner">
        <div class="profile-info">
            <img class="profile-img" src="{{ asset('storage/item_images/' . ($user->profile_image ?? 'default.png')) }}"
                alt="プロフィール画像" width="100" onerror="this.src='{{ asset('storage/item_images/default.png') }}'">
            <div class="name-box">
                <p class="user-name">{{ $user->name }}</p>

                {{-- 評価平均（星5つ） --}}
                @if (!is_null($ratingAvg))
                <div class="rating-stars">

                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <=$ratingAvg)
                        {{-- 色のついた星 --}}
                        <img src="{{ asset('storage/item_images/Star 8.png') }}" class="profile-star">
                        @else
                        {{-- 色なし星 --}}
                        <img src="{{ asset('storage/item_images/Star 9.png') }}" class="profile-star">
                        @endif
                        @endfor
                </div>
                @endif
            </div>
        </div>


        <div class="profile-edit">
            <a href="{{ route('profile.edit') }}" class="edit-button">プロフィールを編集</a>
        </div>
    </div>


    <div class="history-wrapper">

        <!-- タイトル -->
        <div class="history-header">
            <a href="{{ route('mypage', ['page' => 'sell']) }}" class="history-title {{ request('page') === 'sell' ? 'active' : '' }}">
                出品した商品
            </a>
            <a href="{{ route('mypage', ['page' => 'buy']) }}" class="history-title {{ request('page') === 'buy' ? 'active' : '' }}">
                購入した商品
            </a>
            <a href="{{ route('mypage', ['page' => 'transaction']) }}"
                class="history-title {{ request('page') === 'transaction' ? 'active' : '' }}">
                取引中の商品

                {{-- 未読が1件以上あれば通知バッジを表示 --}}
                @if(!empty($totalUnread) && $totalUnread > 0)
                <span class="notification-badge">{{ $totalUnread }}</span>
                @endif
            </a>
        </div>
        <hr>

        <!-- 出品商品一覧 -->
        @if (request('page') === 'sell')
        <div class="history-grid">
            @foreach ($sellItems as $item)
            <div class="item-card">
                <a class="item-card-inner" href="{{ route('items.show', $item->id) }}">
                    <img class="item-card-img" src="{{ asset('storage/item_images/' . $item->image) }}" alt="{{ $item->name }}">
                    <p class="item-name">{{ $item->name }}</p>
                </a>
            </div>
            @endforeach
        </div>

        <!-- 購入商品一覧 -->
        @elseif (request('page') === 'buy')
        <div class="history-grid">
            @foreach ($buyItems as $item)
            <div class="item-card">
                <a class="item-card-inner" href="{{ route('items.show', $item->id) }}">
                    <img class="item-card-img" src="{{ asset('storage/item_images/' . $item->image) }}" alt="{{ $item->name }}">
                    <p class="item-name">{{ $item->name }}</p>
                </a>
            </div>
            @endforeach
        </div>
        @endif

        {{-- 取引中の商品 --}}
        @if (request('page') === 'transaction')
        <div class="history-grid">
            @foreach ($transactionItems as $item)
            <div class="item-card">

                {{-- 取引チャット画面へ遷移 --}}
                <a class="item-card-inner" href="{{ route('transaction.show', $item->id) }}">

                    {{-- 新規メッセージバッジ（未読件数） --}}
                    @if ($item->unread_count ?? false)
                    <div class="notification-badge-2">
                        {{ $item->unread_count }}
                    </div>
                    @endif

                    <img class="item-card-img"
                        src="{{ asset('storage/item_images/' . $item->image) }}"
                        alt="{{ $item->name }}">

                    <p class="item-name">{{ $item->name }}</p>
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection