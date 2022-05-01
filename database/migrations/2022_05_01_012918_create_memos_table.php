<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    //  マイグレーション実行する時の処理
    public function up()
    {
        Schema::create('memos', function (Blueprint $table) {
            $table->unsignedBigInteger('id',true); //とりあえずidはunsigned  trueを入れとくと連番になる
            $table->longText('content');
            $table->unsignedBigInteger('user_id');
            //論理削除を定義-deleted_atを自動生成
            //論理削除:データベース自体からは消えていない事実上の削除状態?
            $table->softDeletes();
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));//メモを更新した時間
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));//メモを作った時間
            // 外部キー成約
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

    //えっととりあえず逆バージョン?
    public function down()
    {
        Schema::dropIfExists('memos');
    }
}
