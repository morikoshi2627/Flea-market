@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="main-inner">

    <form action="{{ route('checkout', $item->id) }}" method="POST" novalidate>
        @csrf

        <input type="hidden" name="postal_code" value="{{ Auth::user()->postal_code }}">
        <input type="hidden" name="address" value="{{ Auth::user()->address }}">
        <input type="hidden" name="building" value="{{ Auth::user()->building }}">


        <div class="main-container">
            <!-- 左側：商品情報 ＋ 支払い方法 -->
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

                <!-- 支払い方法 -->
                <div class="section">
                    <div class="payment">
                        <label class="sub-title">支払い方法</label>
                        <select class="pay-select" name="payment_method" required>
                            <option value="">選択してください</option>
                            <option value="credit" {{ old('payment_method') === 'credit' ? 'selected' : '' }}>カード払い</option>
                            <option value="convenience" {{ old('payment_method') === 'convenience' ? 'selected' : '' }}>コンビニ払い</option>
                        </select>
                        @error('payment_method') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <!-- 配送先表示 -->

                    <hr class="border">

                    <!-- 配送先情報＋変更ボタンの外枠 -->
                    <div class="address-block">
                        <div class="address-info">
                            <label class="sub-title">配送先</label>
                            <p>
                                {{ Auth::user()->postal_code ?? '未登録' }}
                            </p>
                            <p>{{ Auth::user()->address ?? '未登録' }}{{ Auth::user()->building ?? '未登録' }}
                            </p>
                        </div>

                        <!-- 右：変更ボタン -->
                        <div class="address-button">
                            <a class="address-edit-link" href="{{ route('purchase.address.edit', $item->id) }}">
                                変更する
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border">

            <!-- 右側：支払い概要 -->
            <div class="right-column">
                <!-- 支払い概要  -->
                <div class="section-box">
                    <div class="info-row">
                        <span class="box-item">商品代金</span>
                        <span class="info-value">¥{{ number_format($item->price) }}</span>
                    </div>

                    <div class="info-row">
                        <span class="box-item">支払い方法</span>
                        <span class="info-value">
                            @php
                            $method = old('payment_method');
                            @endphp

                            @if ($method === 'credit')
                            カード払い
                            @elseif ($method === 'convenience')
                            コンビニ払い
                            @else
                            選択されていません
                            @endif
                        </span>
                    </div>
                </div>

                <button class="submit-button" type="submit">購入する</button>
            </div>
        </div>
    </form>
</div>
@endsection