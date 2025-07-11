@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<h2>商品の出品</h2>

<!-- セッションに success というキーが存在しているか -->
@if (session('success'))
<p class="success">{{ session('success') }}</p>
@endif

<form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!--  商品画像  -->
    <div>
        <label>商品画像</label>
        <input type="file" name="image" accept="image/jpeg,image/png">
        @error('image')
        <div class="form-error">
            {{ $message }}
        </div>
        @enderror
    </div>

    <h3>商品の詳細</h3>

    <!--  カテゴリー（複数選択　 -->
    <div>
        <label>カテゴリー</label>
        @foreach ($categories as $category)
        <label>
            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
            {{ $category->name }}
        </label>
        @endforeach
        @error('categories')
        <div class="form-error">
            {{ $message }}
        </div>
        @enderror
    </div>

    <!--  商品の状態  -->
    <div>
        <label>商品の状態</label>
        <select name="condition_id">
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

    <h3>商品名と説明</h3>

    <!--  商品名  -->
    <div>
        <label>商品名</label>
        <input type="text" name="name" value="{{ old('name') }}">
        @error('name')
        <div class="form-error">
            {{ $message }}
        </div>
        @enderror
    </div>

    <!--  ブランド名（任意　 -->
    <div>
        <label>ブランド名</label>
        <input type="text" name="brand" value="{{ old('brand') }}">
        @error('brand')
        <div class="form-error">
            {{ $message }}
        </div>
        @enderror
    </div>

    <!--  商品説明 -->
    <div>
        <label>商品の説明</label>
        <textarea name="description" rows="4">{{ old('description') }}</textarea>
        @error('description')
        <div class="form-error">
            {{ $message }}
        </div>
        @enderror
    </div>

    <!-- 商品価格 -->
    <div>
        <label>販売価格（円）</label>
        <input type="number" name="price" min="0" value="{{ old('price') }}">
        @error('price')
        <div class="form-error">
            {{ $message }}
        </div>
        @enderror
    </div>

    <button type="submit">出品する</button>
</form>
</div>
@endsection