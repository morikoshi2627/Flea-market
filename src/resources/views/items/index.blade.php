@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="main-inner">

    <div class="link-area">
        <!-- ルートは後で！！?? -->
        <a class="recommendation-button" href="{{ route('items.index') }}">おすすめ</a>

        <!-- マイリストに遷移するリンクに検索状態を引き継がせる -->
        <a class="mylist-button" href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}">マイリスト</a>
    </div>

    <!-- 商品詳細ページへの遷移 -->
    <div class="item-list">
        @foreach ($items as $item)
        <div class="item-card">
            <a class="item-name" href="{{ route('items.show', ['item' => $item->id]) }}">
                <img class="goods-img" src="{{ asset('storage/item_images/' . $item->image) }}" alt="{{ $item->name }}">
                <p>{{ $item->name }}</p>
            </a>
        </div>
        @endforeach
    </div>



    <!-- ページネーション -->
    <div class="custom-pagination">
        {{ $items->links() }}
    </div>

</div>
@endsection