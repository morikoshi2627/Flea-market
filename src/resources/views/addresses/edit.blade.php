@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endsection

@section('content')
<div class="main-inner">
    <div class="content">
        <h2 class="content-title">住所の変更</h2>

        <form class="form-edit" action="{{ route('purchase.address.update', $item->id) }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="label-title">郵便番号</label>
                <input class="input-area" type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
                @error('postal_code')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="label-title">住所</label>
                <input class="input-area" type="text" name="address" value="{{ old('address', $user->address) }}">
                @error('address')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="label-title">建物名</label>
                <input class="input-area" type="text" name="building" value="{{ old('building', $user->building) }}">
            </div>

            <div class="button-group">
                <button class="submit-button" type="submit">変更する</button>
            </div>
        </form>
    </div>
</div>
@endsection