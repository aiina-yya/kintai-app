<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\AttendanceBreak;
use App\Models\AttendanceCorrection;

class Attendance extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(AttendanceBreak::class);
    }

    public function getBreakMinutesAttribute()
    {
        $total = 0;

        foreach ($this->breaks as $break) {
            if ($break->break_end) {
                $total += $break->break_end->diffInMinutes($break->break_start);
            }
        }

        return $total;
    }

    public function correctionRequest()
    {
        return $this->hasOne(AttendanceCorrection::class);
    }

    public function hasPendingCorrection()
    {
        return $this->correctionRequest()
        ->where('is_approved', 'false')
        ->exists();
    }

    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in',
        'clock_out',
    ];

    protected $casts = [
        'work_date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];
}
