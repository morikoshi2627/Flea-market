@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="main-inner">
  <h1>マイリスト</h1>
  <p>ログインユーザーが「いいね」した商品を表示します。</p>
</div>
@endsection