<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            // 主キー (BIGINT)
            $table->bigIncrements('id');
            
            // FK: users.id (INT) に型を合わせる (※users.idの実際の型がBIGINTの場合はこちらもbigIntegerに修正が必要です)
            $table->integer('user_id')->unsigned(); 
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // 【★ここを修正★】services.id (BIGINT) に型を合わせる
            $table->bigInteger('service_id')->unsigned(); 
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            
            $table->text('description');
            $table->string('tel', 20)->nullable();
            $table->string('email', 100);
            $table->date('deadline')->nullable();
            
            // 0=掲載中,1=進行中,2=完了,3=削除
            $table->tinyInteger('status')->default(0);
            
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
        Schema::dropIfExists('requests');
    }
}