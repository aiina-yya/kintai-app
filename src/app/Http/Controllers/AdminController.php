<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

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

    public function attendanceDetail()
    {

    }

    public function attendanceUpdate()
    {
        
    }

    public function staffLIst()
    {

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
