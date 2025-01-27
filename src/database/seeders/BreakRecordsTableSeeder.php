<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class BreakRecordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'attendance_id' => 1,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ];
        DB::table('break_records')->insert($param);

         $param = [
            'attendance_id' => 1,
            'break_start' => '12:30:00',
            'break_end' => '13:00:00',
        ];
        DB::table('break_records')->insert($param);

         $param = [
            'attendance_id' => 3,
            'break_start' => '12:10:00',
            'break_end' => '13:00:00',
        ];
        DB::table('break_records')->insert($param);
    }
}
