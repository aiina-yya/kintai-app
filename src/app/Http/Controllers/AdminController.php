<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\User;
use App\Models\AttendanceCorrection;
use App\Http\Requests\AdminAttendanceUpdateRequest;

class AdminController extends Controller
{
    public function attendanceList(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();

        $attendances = Attendance::with(['user', 'breaks'])
        ->whereDate('work_date', $date)
        ->get();

        return view('admin.attendance_list',  compact('attendances', 'date'));

    }

    public function attendanceDetail($id)
    {
        $attendance = Attendance::with([
            'user',
            'breaks',
            'correctionRequest',
        ])->findOrFail($id);

        return view('admin.attendance_detail', compact('attendance'));
    }

    public function attendanceUpdate(AdminAttendanceUpdateRequest $request, Attendance $attendance)
    {
        $attendance->update([
            'clock_in' => $attendance->work_date->format('Y-m-d') . ' ' . $request->clock_in,
            'clock_out' => $attendance->work_date->format('Y-m-d') . ' ' . $request->clock_out,
        ]);

        $totalBreakMinutes = 0;

        foreach ($request->break_ids as $index => $breakId) {
            $break = AttendanceBreak::findOrFail($breakId);

            $break->update([
                'break_start' => $attendance->work_date->format('Y-m-d') . ' ' . $request->break_start[$index],
                'break_end' => $attendance->work_date->format('Y-m-d') . ' ' . $request->break_end[$index],
            ]);

            $totalBreakMinutes += $break->break_end->diffInMinutes($break->break_start);


        }

        $workMinutes = $attendance->clock_out->diffInMinutes($attendance->clock_in) - $totalBreakMinutes;

        $attendance->update([
            'work_minutes' => $workMinutes,
            ]);

        return redirect()->route('admin.attendance.list');
    }

    public function staffList()
    {
        $users = User::all();

        return view('admin.staff_list', compact('users'));

    }

    public function staffAttendanceList($id)
    {
        $user = User::findOrFail($id);

        $year = request('year', now()->year);
        $month = request('month', now()->month);

        $attendances = Attendance::with('breaks')->where('user_id', $user->id)
        ->whereYear('work_date',$year)
        ->whereMonth('work_date', $month)
        ->orderBy('work_date', 'desc')
        ->get();

        return view('admin.attendance_staff', compact ('user', 'attendances', 'year', 'month'));

    }

    public function correctionRequestList()
    {
        $status = request('status', 'pending');

        $query = AttendanceCorrection::with('attendance.user');

        if($status === 'pending') {
            $query->where('is_approved', false);
        } else {
            $query->where('is_approved', true);
        }

        $requests = $query->latest()->get();

        return view('admin.correction_request_list', compact('requests', 'status'));
    }

    public function exportCsv($id)
    {
        $user = User::findOrFail($id);

        $year = request('year', now()->year);
    }

    public function correctionApproveView($attendance_correction_id)
    {
        $correction = AttendanceCorrection::with([
            'attendance.user',
            'breaks'
        ])->findOrFail($attendance_correction_id);

        return view('admin.correction_approve', compact('correction'));
    }

    public function approve($attendance_correction_id)
    {
        $correction = AttendanceCorrection::with('breaks')->findOrFail($attendance_correction_id);

        $attendance = $correction->attendance;

        $attendance->update([
            'clock_in' => $correction->requested_clock_in,
            'clock_out' => $correction->requested_clock_out,
        ]);

        foreach ($correction->breaks as $correctionBreak) {
            $attendanceBreak = $attendance->breaks()
            ->where('id', $correctionBreak->attendance_break_id)
            ->first();

            if($attendanceBreak) {
                $attendanceBreak->update([
                    'break_start' => $correctionBreak->requested_break_start,
                    'break_end' => $correctionBreak->requested_break_end,
                ]);
            }
        }

        $correction->update([
            'is_approved' => true,
        ]);

        return redirect()
        ->route('correction.list');
    }
}
