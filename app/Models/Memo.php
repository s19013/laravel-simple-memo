<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    use HasFactory;

    public function getMymemo()
    {
        //クエリパラメータtagがあればタグで絞り込む
        //なければすべて取得
        $query_tag =\Request::query('tag');
        // 条件に応じてクエリビルダーを追加
        //クエリビルダーは分割できる

        $query = Memo::query()->select('memos.*')
        ->Where('user_id','=',\Auth::id())
        ->WhereNull('deleted_at')
        ->orderBy('updated_at','DESC');

        if (!empty($query_tag)) {
            // ここで更にクエリを追加
            $query->leftJoin('memo_tags','memo_tags.memo_id','=','memos.id')
            ->where('memo_tags.tag_id','=',$query_tag);
        }

        $memos = $query->get();
        return $memos;
    }
}
