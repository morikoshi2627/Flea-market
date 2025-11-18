@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat/index.css') }}" />
@endsection

@section('content')
<div class="chat-wrapper">

    {{-- ▼ 左側：その他の取引一覧 --}}
    <div class="sidebar">
        <h2 class="sidebar-h2">その他の取引</h2>

        @forelse ($otherTrades as $trade)
        <div class="trade-item">
            <form action="{{ route('chat.draft', ['item' => $item->id]) }}" method="GET">
                <input type="hidden" name="message" value="{{ old('message', $draft) }}">
                <input type="hidden" name="redirect_to" value="{{ route('transaction.show', $trade->id) }}">

                <a href="{{ route('chat.draft', ['item' => $item->id,'message' => request()->input('message'),'redirect_to' => route('transaction.show', $trade->id),]) }}" class="trade-item-a">
                    {{ $trade->name }}
                </a>
            </form>
        </div>
        @empty
        @endforelse
    </div>


    {{-- ▼ 右側：チャットメイン --}}
    <div class="chat-main">

        <div class="chat-header">
            <div class="header-user-info">
                <img class="header-profile-icon"
                    src="{{ asset('storage/item_images/' . ($partner->profile_image ?? 'default.png')) }}"
                    alt="{{ $partner->name }}のプロフィール画像">

                <h1>「{{ $partner->name }}」さんとの取引画面</h1>
            </div>
            {{-- 取引完了ボタン --}}
            @if (auth()->id() === $item->buyer_id)
            <a href="{{ route('rating.create', $item->id) }}" class="button-complete">
                取引を完了する
            </a>
            @endif
        </div>

        {{-- 商品情報 --}}
        <div class="item-info">
            <img src="{{ asset('storage/item_images/' . $item->image) }}" alt="商品画像" class="item-info-img">

            <div>
                <h3 class="item-name">{{ $item->name }}</h3>
                <p class="item-price">¥{{ number_format($item->price) }}</p>
            </div>
        </div>


        {{-- チャット一覧 --}}
        <div class="messages">
            @foreach ($messages as $msg)

            {{-- 相手のメッセージ --}}
            @if ($msg->user_id !== auth()->id())
            <div class="message-left">
                <div class="message-user">
                    <img class="profile-icon"
                        src="{{ asset('storage/item_images/' . ($msg->user->profile_image ?? 'default.png')) }}"
                        alt="{{ $msg->user->name }}のプロフィール画像">
                    <strong>{{ $msg->user->name }}</strong>
                </div>

                <p class="message-text">{{ $msg->message }}</p>

                @if ($msg->image)
                <img src="{{ asset('storage/' . $msg->image) }}" class="message-img">
                @endif
            </div>

            {{-- 自分のメッセージ --}}
            @else
            <div class="message-right">

                {{-- 編集モードかどうか判定 --}}
                @if ($editId == $msg->id)

                {{-- 編集フォーム --}}
                <form action="{{ route('chat.update', ['item' => $item->id, 'message' => $msg->id]) }}"
                    method="POST" class="edit-form">
                    @csrf
                    @method('PUT')

                    <input type="text" name="message"
                        value="{{ old('message', $msg->message) }}"
                        class="edit-input">

                    <div class="edit-buttons">
                        <button type="submit" class="save-edit-button">保存</button>

                        <a href="{{ route('transaction.show', $item->id) }}"
                            class="cancel-edit-button">
                            キャンセル
                        </a>
                    </div>
                </form>

                @else

                {{-- 通常モード（メッセージ表示） --}}
                <div class="message-user-self">
                    <strong>{{ auth()->user()->name }}</strong>
                    <img class="profile-icon"
                        src="{{ asset('storage/item_images/' . (auth()->user()->profile_image ?? 'default.png')) }}"
                        alt="{{ auth()->user()->name }}のプロフィール画像">
                </div>

                <p class="message-text">{{ $msg->message }}</p>

                @if ($msg->image)
                <img src="{{ asset('storage/' . $msg->image) }}" class="message-img">
                @endif

                {{-- 編集・削除ボタン --}}
                <div class="message-actions">

                    {{-- 編集ボタン（GETで自画面再表示） --}}
                    <a href="{{ route('transaction.show', ['item' => $item->id, 'edit' => $msg->id]) }}"
                        class="edit-button">
                        編集
                    </a>

                    <form action="{{ route('chat.destroy', ['item' => $item->id, 'message' => $msg->id]) }}"
                        method="POST" class="inline-form"
                        onsubmit="return confirm('削除しますか？');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-button">削除</button>
                    </form>
                </div>

                @endif
            </div>
            @endif

            @endforeach
        </div>

        {{-- ▼ 投稿フォーム --}}
        <form class="chat-form" action="{{ route('chat.store', $item->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            @error('message')
            <div class="form-error">
                {{ $message }}
            </div>
            @enderror
            <div class="chat-form-area">
                <input type="text" name="message" placeholder="取引メッセージを記入してください" value="{{ $draft ?? old('message') }}" class="chat-form-input">

                <!-- カスタムボタン -->
                <div class="custom-file">
                    <label class="custom-file-label" for="image">画像を追加</label>
                    <input class="custom-file-input" type="file" name="image" id="image" accept="image/jpeg,image/png" class="custom-file-input">
                </div>

                <button class="button-primary"><img src="{{ asset('storage/item_images/inputbuttun-1.png') }}" alt="送信" class="send-icon"></button>
            </div>
        </form>


        {{-- ★ 評価モーダル --}}
        @if (session('show_rating_modal'))

        <div id="rating-modal" class="rating-modal-wrapper">

            <div class="rating-modal">

                <h3 class="rating-title">取引が完了しました。</h3>
                <p class="rating-subtitle">今回の取引相手はどうでしたか？</p>

                <form action="{{ route('rating.store', $item->id) }}" method="POST" class="rating-form">
                    @csrf

                    {{-- 評価の相手 --}}
                    <input type="hidden" name="rated_id" value="{{ $partner->id }}">

                    {{-- ★ 星評価 --}}
                    <div class="star-rating">

                        @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" name="score" id="star{{ $i }}" value="{{ $i }}">
                        <label for="star{{ $i }}" class="star-label"></label>
                        @endfor

                    </div>

                    <div class="submit-button">
                        <button type="submit" class="rating-submit">送信する</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection