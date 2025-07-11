@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/setting.css') }}">
@endsection

@section('content')
<div class="main-inner">
    <h2>プロフィール編集</h2>

    @if (session('success'))
    <p class="success">{{ session('success') }}</p>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div>
            <label>プロフィール画像</label><br>
            @if ($user->profile_image)
            <img src="{{ asset('storage/item_images/' . ($user->profile_image ?? 'default.png')) }}"
                alt="現在の画像" width="100" onerror="this.src='{{ asset('storage/item_images/default.png') }}'">
            @endif
            <input type="file" name="profile_image">
            @error('profile_image')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div>
            <label>ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}">
            @error('name')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div>
            <label>郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" placeholder="000-0000">
            @error('postal_code')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div>
            <label>住所</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}">
            @error('address')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div>
            <label>建物名</label>
            <input type="text" name="building" value="{{ old('building', $user->building) }}">
            @error('building')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
        </div>

        <button type="submit">更新する</button>
    </form>
</div>
@endsection