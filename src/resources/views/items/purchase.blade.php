@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="main-inner">

    <div class="main-container">

        <!-- 左側：商品情報 + 支払い方法 -->
        <div class="left-column">

            <!-- 商品情報 -->
            <div class="item-summary">
                <img class="item-image" src="{{ asset('storage/item_images/' . $item->image) }}" alt="{{ $item->name }}">
                <div class="item-info">
                    <h2 class="item-name">{{ $item->name }}</h2>
                    <p class="item-price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            <hr class="border">

            <!-- 支払い方法選択フォーム -->
            <div class="section">
                <div class="payment">
                    <label class="sub-title">支払い方法</label>

                    <form method="GET" action="{{ route('purchase.create', $item->id) }}">
                        <div class="custom-select">
                            <select class="payment-select" name="payment_method" onchange="this.form.submit()">
                                <option value="" {{ empty($payment_method) ? 'selected' : '' }}>選択してください</option>
                                <option value="credit" {{ $payment_method === 'credit' ? 'selected' : '' }}>カード払い</option>
                                <option value="konbini" {{ $payment_method === 'konbini' ? 'selected' : '' }}>コンビニ払い</option>
                            </select>
                        </div>
                    </form>
                </div>

                <hr class="border">

                <!-- 配送先情報 -->
                <div class="address-block">
                    <div class="address-info">
                        <label class="sub-title">配送先</label>
                        <p class="address-area">{{ Auth::user()->postal_code ?? '未登録' }}</p>
                        <p class="building-area">{{ Auth::user()->address ?? '未登録' }}{{ Auth::user()->building ?? '' }}</p>
                    </div>

                    <div class="address-button">
                        <a class="address-edit-link" href="{{ route('purchase.address.edit', $item->id) }}">
                            変更する
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <hr class="border">

        <!-- 右側：支払い概要 + 購入ボタン -->
        <div class="right-column">
            <div class="section-box">
                <div class="info-row">
                    <span class="box-item">商品代金</span>
                    <span class="info-value">¥{{ number_format($item->price) }}</span>
                </div>

                <div class="info-row">
                    <span class="box-item">支払い方法</span>
                    <span class="info-value">
                        {{ ($payment_method ?? '未選択') === 'credit' ? 'カード払い' :
                           (($payment_method ?? '') === 'konbini' ? 'コンビニ払い' : '選択されていません') }}
                    </span>
                </div>
            </div>

            <!-- 購入ボタンのみのフォーム -->
            <form action="{{ route('checkout', $item->id) }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method" value="{{ $payment_method }}">
                <input type="hidden" name="postal_code" value="{{ Auth::user()->postal_code }}">
                <input type="hidden" name="address" value="{{ Auth::user()->address }}">
                <input type="hidden" name="building" value="{{ Auth::user()->building }}">
                <button class="submit-button" type="submit">購入する</button>
            </form>
        </div>

    </div>
</div>
@endsection