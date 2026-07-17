<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\AttendanceCorrectionBreak;
use App\Http\Requests\AttendanceCorrectionRequest;

class AttendanceCorrectionController extends Controller
{
    public function index(Request $request)
    {
        if (auth('admin')->check()) {
            return app(AdminController::class)->correctionRequestList($request);
        }

        if (auth()->check()) {
            return $this->show($request);
        }

        abort(403);
    }

    public function show(Request $request)
    {
        $status = $request->query('status' , 'pending');

        $requests = AttendanceCorrection::with('attendance.user')
        ->whereHas('attendance', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->when($status === 'pending', function ($query) {
            $query->where('is_approved', false);
        }, function ($query){
            $query->where('is_approved', true);
        })
        ->latest()
        ->get();

        return view('user.correction_request_list', compact('requests', 'status'));
    }

    public function store(AttendanceCorrectionRequest $request, Attendance $attendance)
    {
        $correction = AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'requested_clock_in' => $attendance->work_date->format('Y-m-d') . ' ' . $request->clock_in,
            'requested_clock_out' => $attendance->work_date->format('Y-m-d') . ' ' . $request->clock_out,
            'reason' => $request->reason,
            'is_approved' => false,
        ]);
        dd($request->break_start, $request->break_end, $request->break_ids);

        foreach ($request->break_start as $i => $start) {
            $correctionBreak = AttendanceCorrectionBreak::create([
                'attendance_correction_id' => $correction->id,
                'attendance_break_id' => $request->break_ids[$i] ?? null,
                'requested_break_start' => $attendance->work_date->format('Y-m-d') .' '.$start,
                'requested_break_end' => $attendance->work_date->format('Y-m-d') .' '.$request->break_end[$i],
            ]);
        }

        return redirect()->route('attendance.list');
    }
}