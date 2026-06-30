<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;

class AttendanceCorrectionController extends Controller
{
    public function show(Request $request)
    {
        $status = $request->query('status' , 'pending');

        $requests = AttendanceCorrection::with('attendance.user')
        ->whereHas('attendance', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->when($status === 'pending', function ($query) {
            $query->where('is_approved', true);
        })
        ->latest()
        ->get();

        return view('user.correction_request_list', compact('requests', 'status'));
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
