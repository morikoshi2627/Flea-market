<!-- @php
use Illuminate\Support\Facades\URL;

$user = Auth::user();
$verifyUrl = URL::temporarySignedRoute(
'verification.verify',
now()->addMinutes(60),
['id' => $user->id, 'hash' => sha1($user->email)]
);
@endphp -->

@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endsection


@section('content')
<div class="main-inner">
    <div class="message-area">
        <div class="message-1">登録していただいたメールアドレスに認証メールを送付しました。</div>
        <p class="message-2">メール認証を完了してください。</p>
    </div>

    <a class="button-primary" href="{{ route('verification.notice') }}">認証はこちらから</a>

    <a href="{{ $verifyUrl }}" class="btn btn-primary" target="_blank">認証はこちらから</a> -->

    <!-- <a href="{{ route('verification.notice') }}" class="btn btn-primary">認証はこちらから</a> -->

    @if (session('status') == 'verification-link-sent')
    <p class="text-success">新しい認証リンクを送信しました！</p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button class="button-resend" type="submit">認証メールを再送する</button>
    </form>
</div>
@endsection