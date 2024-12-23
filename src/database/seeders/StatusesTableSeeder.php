<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; //追加

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'status' => '承認待ち'
        ];
        DB::table('statuses')->insert($param);

        $param = [
            'status' => '承認済み'
        ];
        DB::table('statuses')->insert($param);
    }
}
