<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceCorrection;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendance = Attendance::where('user_id', Auth::id())
        ->where('work_date', today())
        ->first();

        $attendanceBreak = null;

        if ($attendance) {
            $attendanceBreak = AttendanceBreak::where('attendance_id', $attendance->id)
            ->latest()
            ->first();
            }

        if (!$attendance) {
            $status = '勤務外';
        } elseif ($attendance->clock_out) {
            $status = '退勤済';
        } elseif ($attendanceBreak && is_null($attendanceBreak->break_end)) {
            $status = '休憩中';
        } else {
            $status = '出勤中';
        }

            return view('user.attendance', compact('status', 'attendance', 'attendanceBreak'));
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

                $clockOut = now();

                $breaks = AttendanceBreak::where(
                    'attendance_id', $attendance->id
                )->get();

                $totalBreakMinutes = 0;

                foreach ($breaks as $break) {
                    if ($break->break_end) {
                        $totalBreakMinutes +=
                        $break->break_end->diffInMinutes(
                            $break->break_start
                        );
                    }
                }

                $workMinutes = $clockOut->diffInMinutes($attendance->clock_in) - $totalBreakMinutes;

                $attendance->update([
                    'clock_out' => $clockOut,
                    'work_minutes' => $workMinutes
                ]);
        }

        return redirect()->route('attendance');
    }

    public function breakStart()
    {
        $attendance = Attendance::where('user_id',Auth::id())
        ->where('work_date', today())
        ->first();

        if(!$attendance) {
            return redirect()->route('attendance');
        }

        AttendanceBreak::create([
            'attendance_id' => $attendance->id,
            'break_start' => now(),
        ]);

        return redirect()->route('attendance');

    }

    public function breakEnd()
    {
        $attendance = Attendance::where('user_id', Auth::id())
        ->where('work_date', today())
        ->first();

        $break = AttendanceBreak::where('attendance_id', $attendance->id)
        ->whereNull('break_end')
        ->latest()
        ->first();

        if($break) {
            $break->update([
                'break_end' => now(),
            ]);
        }

        return redirect()->route('attendance');

    }

    public function attendanceList()
    {
        $year = request('year', now()->year);
        $month = request('month', now()->month);

        $attendances = Attendance::with('breaks')
        ->where('user_id', Auth::id())
        ->whereYear('work_date', $year)
        ->whereMonth('work_date', $month)
        ->orderBy('work_date', 'desc')
        ->get();

        return view('user.attendance_list', compact('attendances', 'year', 'month'));

    }

    public function attendanceDetail(Attendance $attendance)
    {
        $attendance->load('user', 'breaks');

        $readonly = request('from') === 'request';

        $correction = null;

        if ($readonly) {
            $correction = AttendanceCorrection::with('breaks')
            ->find(request('correction'));
        }

        return view('user.attendance_detail', compact('attendance','readonly', 'correction'));
    }
}
