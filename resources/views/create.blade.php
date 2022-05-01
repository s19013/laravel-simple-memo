@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">新規メモ作成</div>
    <form class="card-body" action="{{route('store')}}" method="POST">
        @csrf
        <div class="form-floating p-1">
            <textarea class="form-control" placeholder="Leave a comment here" rows="3" name="content"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">保存</button>
    </form>
  </div>
@endsection
