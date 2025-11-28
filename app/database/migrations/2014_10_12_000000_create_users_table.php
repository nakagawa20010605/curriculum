<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // 既存カラムの修正・確認
            $table->string('name', 20); // VARCHAR(20)
            $table->string('email', 50)->unique(); // VARCHAR(50)
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 100); // VARCHAR(100)
            $table->rememberToken(); // ログイン維持用トークン

            // 設計書に基づいて追加するカラム
            $table->string('image')->nullable(); // ユーザーアイコン
            Schema::create('users', function (Blueprint $table) {
                // 既存の他のカラム...
                $table->string('image')->nullable()->after('password')->comment('ユーザーアイコンのファイルパス'); // この行があるか確認
                // 既存の他のカラム...
            });
            
            $table->tinyInteger('role')->default(0)->comment('一般=0/管理=1'); // ユーザー区分
            $table->tinyInteger('del_flg')->default(0)->comment('表示=0/削除=1'); // 削除フラグ
            $table->tinyInteger('stop_flg')->default(0)->comment('表示=0/利用停止=1'); // 利用停止フラグ

            // タイムスタンプ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
