<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class AttendancesRequestsTableSeeder extends Seeder
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
            'status_id' => '1',
            'pending_reason' => '電車遅延のため',
            'approved_at' => null
        ];
        DB::table('attendance_requests')->insert($param);
    }
}
