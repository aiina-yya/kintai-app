<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\User;
use App\Models\AttendanceCorrection;
use App\Http\Requests\AdminAttendanceUpdateRequest;
use App\Models\AttendanceBreak;

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

    public function attendanceUpdate(AdminAttendanceUpdateRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'clock_in' => $attendance->work_date->format('Y-m-d') . ' ' . $request->clock_in,
            'clock_out' => $attendance->work_date->format('Y-m-d') . ' ' . $request->clock_out,
            'reason' => $request->reason,
        ]);

        $totalBreakMinutes = 0;

        foreach ($request->break_ids as $index => $breakId) {
            $break = AttendanceBreak::findOrFail($breakId);

            $break->update([
                'break_start' => $attendance->work_date->format('Y-m-d') . ' ' . $request->break_start[$index],
                'break_end' => $attendance->work_date->format('Y-m-d') . ' ' . $request->break_end[$index],
            ]);

            $newIndex = count($request->break_ids);

            if (!empty($request->break_start[$request->break_ids ? count($request->break_ids) : 0])) {
                $attendance->breaks()->create([
                    'break_start' => $attendance->work_date->format('Y-m-d') . ' ' . $request->break_start[$newIndex],
                    'break_end' => $attendance->work_date->format('Y-m-d') . ' ' . $request->break_end[$newIndex],
                ]);
                }

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
        $month = request('month', now()->month);

        $attendances = Attendance::with('breaks')
        ->where('user_id', $user->id)
        ->whereYear('work_date', $year)
        ->whereMonth('work_date', $month)
        ->orderBy('work_date', 'desc')
        ->get();

        return response()->streamDownload(function () use ($attendances, $user, $year, $month) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['日付', '出勤', '退勤', '休憩', '合計']);

            foreach ($attendances as $attendance) {

                $breakMinutes = 0;

                foreach ($attendance->breaks as $break) {

                    if ($break->break_end) {
                        $breakMinutes += $break->break_end->diffInMinutes($break->break_start);
                    }
                }
                $breakTime = floor($breakMinutes / 60) . ':' . sprintf('%02d', $breakMinutes % 60);

                $workTime = $attendance->work_minutes ? floor($attendance->work_minutes / 60) . ':' . sprintf('%02d', $attendance->work_minutes % 60) : '';

            fputcsv($handle, [
                $attendance->work_date->format('Y-m-d'),
                optional($attendance->clock_in)->format('H:i'),
                optional($attendance->clock_out)->format('H:i'),
                $breakTime,
                $workTime,
            ]);
        }

        fclose($handle);

        }, "$user->name . _{$year}_{$month}.csv"
        );
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
            if ($correctionBreak->attendance_break_id) {
                $attendanceBreak = $attendance->breaks()
                ->where('id', $correctionBreak->attendance_break_id)
                ->first();

                if($attendanceBreak) {
                    $attendanceBreak->update([
                        'break_start' => $correctionBreak->requested_break_start,
                        'break_end' => $correctionBreak->requested_break_end,
                    ]);
                }
            } else {
            $attendance->breaks()->create([
                'break_start' => $correctionBreak->requested_break_start,
                'break_end' => $correctionBreak->requested_break_end]);
            }

            $correction->update([
                'is_approved' => true,
            ]);

            return redirect()
            ->route('correction.list');
        }
    }
}