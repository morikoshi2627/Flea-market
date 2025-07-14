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

            <p class="user-name">
                <strong class="user-name-strong">ユーザー名</strong>{{ $user->name }}</p>

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
            @if($item->item)
            <div class="item-card">
                <a class="item-card-inner" href="{{ route('items.show', $item->item->id) }}">
                    <img class="item-card-img" src="{{ asset('storage/item_images/' . $item->item->image) }}" alt="{{ $item->item->name }}">
                    <p class="item-name">{{ $item->item->name }}</p>
                </a>
            </div>
            @endif
            @endforeach
        </div>
        @endif

    </div>
</div>
@endsection