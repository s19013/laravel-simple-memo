<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Memo;

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
            $memos = Memo::select('memos.*')
            ->Where('user_id','=',\Auth::id())
            ->WhereNull('deleted_at')
            ->orderBy('updated_at','DESC')
            ->get();

            //ビューにわたす
            //第1引数はViewで使う時の命名
            //第2引数はわたしたい変数or配列名
            $view->with('memos',$memos);
        });

    }
}
