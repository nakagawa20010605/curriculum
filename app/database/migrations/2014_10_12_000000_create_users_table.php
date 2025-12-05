<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            // #1 ID (PK, INT, AUTO_INCREMENT)
            $table->increments('id');

            // #2 name (VARCHAR(20), Unique)
            $table->string('name', 20)->unique();
            
            // #3 email (VARCHAR(50), Unique)
            $table->string('email', 50)->unique();
            
            // #4 password (VARCHAR(100), NotNull)
            $table->string('password', 100);
            
            // #5 remember_token (VARCHAR(100))
            $table->rememberToken();

            // #6 image (VARCHAR)
            $table->string('image')->nullable();
            
            // #7 role (tinyint, NotNull, Default 0)
            // 0: 一般 / 1: 管理
            $table->tinyInteger('role')->default(0)->index();
            
            // #8 del_flg (tinyint, NotNull, Default 0)
            // 削除フラグ（ユーザー用） 0: 表示 / 1: 削除
            $table->tinyInteger('del_flg')->default(0);

            // #9 stop_flg (tinyint, NotNull, Default 0)
            // 削除フラグ（管理者用） 0: 表示 / 1: 利用停止
            $table->tinyInteger('stop_flg')->default(0);
            
            // #10 created_at & #11 updated_at (date型)
            // 通常はtimestamps()ですが、設計書に合わせてdate型を明示
            $table->date('created_at')->nullable();
            $table->date('updated_at')->nullable();
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