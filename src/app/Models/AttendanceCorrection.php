<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
