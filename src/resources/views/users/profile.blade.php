@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="main-inner">
    <h2>プロフィール</h2>

    <div class="profile-info">
        <img src="{{ asset('storage/item_images/' . ($user->profile_image ?? 'default.png')) }}"
            alt="プロフィール画像" width="100" onerror="this.src='{{ asset('storage/item_images/default.png') }}'">
        <p><strong>名前：</strong>{{ $user->name }}</p>
        <p><strong>郵便番号：</strong>{{ $user->postal_code }}</p>
        <p><strong>住所：</strong>{{ $user->address }}</p>
        <p><strong>建物名：</strong>{{ $user->building }}</p>
    </div>

    <a href="{{ route('profile.edit') }}">プロフィールを編集</a>


    <hr>

    <h3>購入した商品一覧</h3>
    <ul>
        @foreach ($buyItems as $item)
        <li>{{ $item->item->name ?? '商品なし' }}</li>
        @endforeach
        <a href="{{ route('mypage', ['page' => 'buy']) }}">購入履歴</a>
    </ul>

    <h3>出品した商品一覧</h3>
    <ul>
        @foreach ($sellItems as $item)
        <li>{{ $item->name }}</li>
        @endforeach
        <a href="{{ route('mypage', ['page' => 'sell']) }}">出品履歴</a>
    </ul>
</div>
@endsection