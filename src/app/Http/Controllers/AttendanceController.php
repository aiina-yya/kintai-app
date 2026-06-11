<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendance = Attendance::where('user_id', Auth::id())
        ->where('work_date', today())
        ->first();

        if (!$attendance) {
            $status = '勤務外';
        } elseif ($attendance->clock_out) {
            $status = '退勤済';
        } else {
            $break = AttendanceBreak::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->first();

            if($break) {
                $status = '休憩中';
            } else {
                $status = '出勤中';
            }
        }

        return view('user.attendance', compact('status', 'attendance'));
    }

    public function clockIn()
    {
        $attendance = Attendance::where('user_id', Auth::id())
        ->where('work_date', today())
        ->first();

        if(!$attendance) {
            Attendance::create([
                'user_id' => Auth::id(),
                'work_date' => today(),
                'clock_in' => now(),
            ]);
        }

        return redirect()->route('attendance');
    }

    public function clockOut()
    {
        $attendance = Attendance::where('user_id', Auth::id())
        ->where('work_date', today())
        ->first();

        if($attendance && !$attendance->clock_out) {
            $attendance->update([
                'clock_out' => now(),
            ]);
        }

        return redirect()->route('attendance');
    }

    public function breakStart()
    {

    }

    public function breakEnd()
    {

    }

    public function attendanceList()
    {

    }

    public function attendanceDetail()
    {

    }

    public function correctionStore()
    {
        
    }
}
