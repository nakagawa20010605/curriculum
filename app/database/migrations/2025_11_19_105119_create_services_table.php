<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     *
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');
            // FK: users.id の型 (INT) に合わせるため、integer()->unsigned() を使用
            $table->integer('user_id')->unsigned(); 
            // 外部キー制約の定義
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade'); 

            
            $table->string('title', 100);
            $table->integer('amount');
            $table->text('description');
            $table->string('image')->nullable();
            
            // 0=掲載中,1=進行中,2=完了,3=削除
            $table->tinyInteger('status')->default(0);
            // ここは timestamp の方がLaravel標準
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
        Schema::dropIfExists('services');
    }
}