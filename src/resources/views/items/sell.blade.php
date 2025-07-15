@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="main-inner">
    <div class="main-title-area">
        <h2 class="main-title">商品の出品</h2>
    </div>

    <!-- セッションに success というキーが存在しているか -->
    @if (session('success'))
    <p class="success">{{ session('success') }}</p>
    @endif

    <form class="form-content" action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!--  商品画像  -->
        <div class="content">
            <label class="title">商品画像</label>
            <!-- カスタムファイル選択ボタン -->
            <div class="custom-file">
                <label class="custom-file-label" for="image">画像を選択する</label>
                <input class="custom-file-input" type="file" name="image" id="image" accept="image/jpeg,image/png" class="custom-file-input">
            </div>
            @error('image')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <h3 class="sub-title">商品の詳細</h3>
        <hr class="hr">

        <!--  カテゴリー（複数選択　 -->
        <div class="content">
            <labe class="title">カテゴリー</label>
                <div class="category-button-group">
                    @foreach ($categories as $category)
                    <label class="category-button">
                        <input class="category-input" type="checkbox" name="categories[]" value="{{ $category->id }}"
                            {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                        <span class="category-button-span">{{ $category->name }}</span>
                    </label>
                    @endforeach
                </div>
                <div class="cartegory-error">
                @error('categories')
                <div class="form-error">
                    {{ $message }}
                </div>
                @enderror
                </div>
        </div>

        <!--  商品の状態  -->
        <div class="content">
            <label class="title">商品の状態</label>
            <select class="content-select" name="condition_id">
                <option value="">選択してください</option>
                @foreach ($conditions as $condition)
                <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                    {{ $condition->name }}
                </option>
                @endforeach
            </select>
            @error('condition_id')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <h3 class="sub-title">商品名と説明</h3>
        <hr class="hr">

        <!--  商品名  -->
        <div class="content">
            <label class="title">商品名</label>
            <input class="content-input" type="text" name="name" value="{{ old('name') }}">
            @error('name')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <!--  ブランド名（任意　 -->
        <div class="content">
            <label class="title">ブランド名</label>
            <input class="content-input" type="text" name="brand" value="{{ old('brand') }}">
            @error('brand')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <!--  商品説明 -->
        <div class="content">
            <label class="title">商品の説明</label>
            <textarea class="content-textarea" name="description" rows="4">{{ old('description') }}</textarea>
            @error('description')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <!-- 商品価格 -->
        <div class="content">
            <label class="title">販売価格（円）</label>
            <div class="price-input-wrapper">
                <span class="yen-mark">¥</span>
                <input class="content-input price-input" type="number" name="price" min="0" value="{{ old('price') }}">
            </div>
            @error('price')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <button class="submit-button" type="submit">出品する</button>
    </form>
</div>
@endsection