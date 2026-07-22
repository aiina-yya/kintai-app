<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionBreak extends Model
{
    public function attendanceCorrection()
    {
        return $this->belongsTo(AttendanceCorrection::class);
    }

    protected $fillable = [
        'attendance_correction_id',
        'attendance_break_id',
        'requested_break_start',
        'requested_break_end',
    ];

    protected $casts = [
        'requested_break_start' => 'datetime',
        'requested_break_end' => 'datetime',
    ];
}
