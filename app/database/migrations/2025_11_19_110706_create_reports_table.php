<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            // プライマリキー
            $table->bigIncrements('id'); 
            
            // 外部キー1: users.id (INT) に合わせる
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // 【★追加したカラム★】
            // 外部キー2: services.id (BIGINT) に合わせる
            $table->bigInteger('service_id')->unsigned();
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');

            $table->text('details')->nullable();
            
            $table->timestamps();
            
            // ユーザーが一つのサービスに対して複数回レポートを送れないように、ユニーク制約を追加することも検討できます。
            // $table->unique(['user_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 外部キー制約を先に解除してからテーブルを削除
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['user_id']); 
            // 【★追加した外部キーも削除★】
            $table->dropForeign(['service_id']); 
        });
        
        Schema::dropIfExists('reports');
    }
}