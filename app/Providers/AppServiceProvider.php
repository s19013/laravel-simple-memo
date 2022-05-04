<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Memo;
use App\Models\Tag;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //すべてのメソッドが呼ばれる前に先に呼ばれるメソッド
        view()->composer('*',function($view) {
            //クエリパラメータtagがあればタグで絞り込む
            //なければすべて取得

            $query_tag =\Request::query('tag');

            if (!empty($query_tag)) {
                $memos = Memo::select('memos.*')
                ->leftJoin('memo_tags','memo_tags.memo_id','=','memos.id')
                ->where('memo_tags.tag_id','=',$query_tag)
                ->Where('user_id','=',\Auth::id())
                ->WhereNull('deleted_at')
                ->orderBy('updated_at','DESC')
                ->get();
            } else {
                $memos = Memo::select('memos.*')
                ->Where('user_id','=',\Auth::id())
                ->WhereNull('deleted_at')
                ->orderBy('updated_at','DESC')
                ->get();
            }
            $tags = Tag::where('user_id','=',\Auth::id())
                ->whereNull('deleted_at')
                ->orderBy('id','DESC')
                ->get();

            //ビューにわたす
            //第1引数はViewで使う時の命名
            //第2引数はわたしたい変数or配列名
            $view
            ->with('memos',$memos)
            ->with('tags' ,$tags);

        });

    }
}
