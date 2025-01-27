<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

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

        $param = [
            'id' => 2,
            'user_id' => 1,
            'date' => '2024-11-1',
            'clock_in_time' => '09:30:00',
            'clock_out_time' => '17:30:00',
        ];
        DB::table('attendances')->insert($param);

        $param = [
            'id' => 3,
            'user_id' => 1,
            'date' => '2024-12-1',
            'clock_in_time' => '09:00:00',
            'clock_out_time' => '18:00:00',
        ];
        DB::table('attendances')->insert($param);
    }
}
