<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\User;

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

    public function attendanceDetail(Attendance $attendance)
    {
        $attendance->load([
            'user',
            'breaks',
            'correctionRequest',
        ]);

        return view('admin.attendance-detail', compact('attendance'));
    }

    public function attendanceUpdate()
    {
    }

    public function staffList()
    {
        $users = User::all();

        return view('admin.staff_list', compact('users'));

    }

    public function staffAttendanceList()
    {

    }

    public function correctionRequestList()
    {

    }

    public function correctionApproveView()
    {

    }

    public function approve()
    {

    }
}
