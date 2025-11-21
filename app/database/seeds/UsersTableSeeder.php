<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@test.com', // 管理者としてログインするためのメールアドレス
            'password' => Hash::make('password'), // 共通のパスワード
            'role' => 1, // ★管理者フラグ
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'name' => 'General User',
            'email' => 'user@test.com', // 一般ユーザーとしてログインするためのメールアドレス
            'password' => Hash::make('password'),
            'role' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
