<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; //追加

class AttendancesTableSeeder extends Seeder
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
            'user_id' => 1,
            'date' => '2024-12-21',
            'clock_in_time' => '09:30:00',
            'clock_out_time' => '17:30:00',
        ];
        DB::table('attendances')->insert($param);
    }
}
