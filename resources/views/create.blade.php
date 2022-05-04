@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">新規メモ作成</div>
    <form class="card-body" action="{{route('store')}}" method="POST">
        @csrf
        <div class="form-floating mb-3">
            <textarea class="form-control" placeholder="メモ本文入力" rows="3" name="content"></textarea>
        </div>
        @foreach ($tags as $t)
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="tags[]" id="{{$t['id']}}" value="{{$t['id']}}">
            <label class="form-check-label" for="{{$t['id']}}">
                {{$t['name']}}
            </label>
        </div>
        @endforeach
        @error('content')
            <div class="alert alert-danger">メモを入力してください</div>
        @enderror
        {{-- このメモ帳必ずなにかタグをつけないとエラーが起こる --}}
        <input type="text" name="new_tag"  class="form-floating p-1 w-50 mb-3" placeholder="新しいタグ入力"><br>
        <button type="submit" class="btn btn-primary">保存</button>
    </form>
  </div>
@endsection
