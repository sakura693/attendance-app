<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; //追加
use Illuminate\Support\Carbon; //追加
use Illuminate\Support\Facades\Hash;//追加

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'id' => 1,
            'name' => '一般ユーザー',
            'email' => 'general@example.com',
            /*現在の日時を取得*/
            'email_verified_at' => Carbon::now(),
            /*パスワードを暗号化する*/
            'password' => Hash::make('password'),
            'role' => 'staff',
        ];
        DB::table('users')->insert($param);

        $param = [
            'id' => 2,
            'name' => '管理者',
            'email' => 'admin@example.com',
            /*現在の日時を取得*/
            'email_verified_at' => Carbon::now(),
            /*パスワードを暗号化する*/
            'password' => Hash::make('password'),
            'role' => 'admin',
        ];
        DB::table('users')->insert($param);
    }
}
