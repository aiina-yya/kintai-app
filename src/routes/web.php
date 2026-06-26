<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceCorrectionController;
use App\Http\Controllers\AdminController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth','verified'])->group(function(){
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance/clock-in',[AttendanceController::class, 'clockIn'])->name('attendance.clockIn');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockOut');
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.breakStart');
    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])->name('attendance.breakEnd');
    Route::get('/attendance/list', [AttendanceController::class, 'attendanceList'])->name('attendance.list');
    Route::get('/attendance/detail/{attendance}', [AttendanceController::class, 'attendanceDetail'])->name('attendance.detail');
    Route::post('/attendance/detail/{attendance}', [AttendanceCorrectionController::class, 'store'])->name('attendance.correction');
    Route::get('/stamp_correction_request/list', [AttendanceCorrectionController::class, 'show'])->name('correction.list');
});

Route::middleware('auth:admin')->group(function() {
    Route::get('admin/attendance/list', [AdminController::class, 'attendanceList'])->name('admin.attendance.list');
    Route::get('/admin/attendance/{id}', [AdminController::class, 'attendanceDetail'])->name('admin.attendance.detail');
    Route::get('/admin/staff/list', [AdminController::class, 'staffList'])->name('admin.staff');
    Route::get('/admin/attendance/staff/{id}', [AdminController::class, 'staffAttendanceList'])->name('admin.attendance.staff');
    //Route::get('/stamp_correction_request/list', [AdminController::class, 'correctionRequestList'])->name('admin.correction');
    Route::get('/stamp_correction_request/approve/{attendance_correction_request_id}', [AdminController::class, 'correctionApproveView'])->name('admin.approve.view');
    Route::patch('/stamp_correction_request/approve/{attendance_correction_request_id}', [AdminController::class, 'approve'])->name('admin.approve');
});