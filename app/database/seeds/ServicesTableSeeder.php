<?php

use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('services')->insert([
            'user_id' => 1,
            'title' => '営業代行',
            'amount' => 70000,
            'description' => '詳細は弊社HPまで',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
