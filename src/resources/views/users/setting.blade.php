@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/setting.css') }}">
@endsection

@section('content')
<div class="main-inner">
    <h2 class="main-title">プロフィール設定</h2>

    @if (session('success'))
    <p class="success">{{ session('success') }}</p>
    @endif

    <form class="profile-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- 画像と選択ボタン -->
        <div class="profile-image-area" style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
            <div>
                <img class="profile-img" src="{{ asset('storage/item_images/' . ($user->profile_image ?? 'default.png')) }}"
                    alt="現在の画像" width="100"
                    onerror="this.src='{{ asset('storage/item_images/default.png') }}'">
            </div>
            <div>
                <input class="profile-img-choice" type="file" name="profile_image">
                @error('profile_image')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- 入力欄 -->
        <div class="form-group">
            <label class="form-label">ユーザー名</label>
            <input class="form-input" type="text" name="name" value="{{ old('name', $user->name) }}">
            @error('name')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">郵便番号</label>
            <input class="form-input" type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" placeholder="000-0000">
            @error('postal_code')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">住所</label>
            <input class="form-input" type="text" name="address" value="{{ old('address', $user->address) }}">
            @error('address')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">建物名</label>
            <input class="form-input" type="text" name="building" value="{{ old('building', $user->building) }}">
            @error('building')
            <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <button class="button-edit" type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection