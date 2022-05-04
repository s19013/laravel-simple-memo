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
        // インスタンス化する
        $memo_model = new Memo();
        //メモ取得
        $memos = $memo_model->getMyMemo();

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
