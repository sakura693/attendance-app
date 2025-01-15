<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignid('attendance_id')->constrained()->cascadeOnDelete();
            $table->foreignid('status_id')->constrained()->cascadeOnDelete();
            $table->date('new_date')->nullable();
            $table->time('new_clock_in_time')->nullable();
            $table->time('new_clock_out_time')->nullable();
            $table->string('pending_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_requests');
    }
}
