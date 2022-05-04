@extends('layouts.app')

@section('javascript')
<script src="/js/confirm.js">

</script>
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        メモ編集
        <form action="{{route('destroy')}}" method="post" class="card-body" id="delete-form">
            @csrf
            <input type="hidden" name="memo_id" value="{{$edit_memo[0]['id']}}"/>
            <button type="submit" class="btn btn-primary p-1" onclick="deleteHandle(event);">削除</button>
        </form>
    </div>
    <form class="card-body" action="{{route('update')}}" method="POST">
        @csrf
        <input type="hidden" name="memo_id" value="{{$edit_memo[0]['id']}}">
        <div class="form-floating p-1">
            <textarea class="form-control" placeholder="Leave a comment here" rows="3" name="content">{{$edit_memo[0]['content']}}</textarea>
        </div>
        @foreach ($tags as $t)
        <div class="form-check">
            {{-- 今回っているtagのidがinclude_tagsの中にあれば checkedを書く --}}
            <input class="form-check-input" type="checkbox" name="tags[]" id="{{$t['id']}}" value="{{$t['id']}}"
            {{ in_array($t['id'],$include_tags) ? 'checked' : ''}}>
            <label class="form-check-label" for="{{$t['id']}}">
                {{$t['name']}}
            </label>
        </div>
        @endforeach
        @error('content')
            <div class="alert alert-danger">メモを入力してください</div>
        @enderror
        <input type="text" name="new_tag"  class="form-floating p-1 w-50 mb-3" placeholder="新しいタグ入力"><br>
        <button type="submit" class="btn btn-primary p-1">更新</button>
    </form>
  </div>
@endsection
