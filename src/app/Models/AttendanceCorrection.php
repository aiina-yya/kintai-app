<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionBreak;

class AttendanceCorrection extends Model
{
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function correctionBreaks()
    {
        return $this->hasMany(AttendanceCorrectionBreak::class);
    }

    protected $fillable = [
        'attendance_id',
        'requested_clock_in',
        'requested_clock_out',
        'reason',
        'is_approved',
    ];
}
