<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendanceBreakIdToAttendanceCorrectionBreaksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_correction_breaks', function (Blueprint $table) {
            $table->foreignId('attendance_break_id')
            ->nullable()
            ->after('attendance_correction_id')
            ->constrained()
            ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_correction_breaks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attendance_break_id');
        });
    }
}
