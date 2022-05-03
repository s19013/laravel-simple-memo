@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">新規メモ作成</div>
    <form class="card-body" action="{{route('store')}}" method="POST">
        @csrf
        <div class="form-floating mb-3">
            <textarea class="form-control" placeholder="メモ本文入力" rows="3" name="content"></textarea>
        </div>
        <input type="text" name="new_tag"  class="form-floating p-1 w-50 mb-3" placeholder="新しいタグ入力"><br>
        <button type="submit" class="btn btn-primary">保存</button>
    </form>
  </div>
@endsection
