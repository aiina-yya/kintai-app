<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;

class AttendanceCorrectionController extends Controller
{
    public function show()
    {
        $requests = AttendanceCorrection::with(['attendance.user'])
        ->latest()
        ->get();

        return view('user.correction_request_list', compact('requests'));
    }

    public function store(Request $request, Attendance $attendance)
    {
        AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'requested_clock_in' => $attendance->work_date->format('Y-m-d') . ' ' . $request->clock_in,
            'requested_clock_out' => $attendance->work_date->format('Y-m-d') . ' ' . $request->clock_out,
            'reason' => $request->reason,
            'is_approved' => false,
        ]);

        return redirect()->route('attendance.list');
    }
}
