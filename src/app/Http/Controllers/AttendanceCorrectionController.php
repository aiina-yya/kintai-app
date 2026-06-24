<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;

class AttendanceCorrectionController extends Controller
{
    public function show()
    {
        $requests = AttendanceCorrection::where('user_id', auth()->id())
        ->latest()
        ->get();

        return view('user.correction_request_list', compact('requests'));
    }

    public function store(Request $request, Attendance $attendance)
    {
        AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'user_id' => auth()->id(),
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('attendance.list');
    }
}
