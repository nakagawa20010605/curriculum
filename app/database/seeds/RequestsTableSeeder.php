<?php

use Illuminate\Database\Seeder;

class RequestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('requests')->insert([
            'user_id' => 3,
            'service_id' => 4,
            'description' => 'よろしくお願いします',
            'email' => 'nakagawa20010605@gmail.com',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
