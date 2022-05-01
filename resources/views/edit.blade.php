@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">メモ編集</div>
    <form class="card-body" action="{{route('update')}}" method="POST">
        @csrf
        <input type="hidden" name="memo_id" value="{{$edit_memo['id']}}">
        <div class="form-floating p-1">
            <textarea class="form-control" placeholder="Leave a comment here" rows="3" name="content">{{$edit_memo['content']}}</textarea>
        </div>
        <button type="submit" class="btn btn-primary p-1">更新</button>
    </form>
  </div>
@endsection
