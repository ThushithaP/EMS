<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaveApprovalController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffController;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
// Route::get('/department/list', [DepartmentController::class, 'list'])->middleware('auth','checkPermission:1')->name('department.list');
Route::middleware(['auth'])->group(function () {
    Route::get('/department', [DepartmentController::class, 'index'])->name('department');
    Route::post('/department/store', [DepartmentController::class, 'store'])->middleware('checkPermission:2'.Permission::ADD_DEPARTMENT)->name('department.store');
    Route::get('/department/list', [DepartmentController::class, 'list'])->middleware('checkPermission:'.Permission::LIST_DEPARTMENT)->name('department.list');
    Route::post('/department/show', [DepartmentController::class, 'show'])->middleware('checkPermission:'.Permission::VIEW_DEPARTMENT)->name('department.show');
    Route::post('/department/update', [DepartmentController::class, 'update'])->middleware('checkPermission:'.Permission::EDIT_DEPARTMENT)->name('department.update');
    Route::post('/department/status', [DepartmentController::class, 'status'])->middleware('checkPermission:'.Permission::STATUS_CHANGE_DEPARTMENT)->name('department.status');
    Route::post('/department/export', [DepartmentController::class, 'export'])->middleware('checkPermission:'.Permission::EXPORT_DEPARTMENT_TO_EXCEL)->name('department.export');

    Route::get('/staff',[StaffController::class,'index'])->name('staff')->middleware('checkPermission:'.Permission::LIST_STAFF);
    Route::post('/staff/store', [StaffController::class,'store'])->middleware('checkPermission:'.Permission::ADD_STAFF)->name('staff.store');
    Route::get('/staff/list', [StaffController::class,'list'])->middleware('checkPermission:'.Permission::LIST_STAFF)->name('staff.list');
    Route::post('/staff/show', [StaffController::class,'show'])->middleware('checkPermission:'.Permission::VIEW_STAFF)->name('staff.show');
    Route::post('/staff/update', [StaffController::class,'update'])->middleware('checkPermission:'.Permission::EDIT_STAFF)->name('staff.update');
    Route::post('/staff/status', [StaffController::class,'status'])->middleware('checkPermission:'.Permission::STATUS_CHANGE_STAFF)->name('staff.status');
    Route::post('/staff/export', [StaffController::class, 'export'])->middleware('checkPermission:'.Permission::EXPORT_STAFF_TO_EXCEL)->name('staff.export');
    Route::post('/staff/permission', [StaffController::class, 'listPermission'])->middleware('checkPermission:'.Permission::CHANGE_STAFF_PERMISSION)->name('staff.permission');
    Route::post('/staff/setpermission', [StaffController::class, 'setPermission'])->middleware('checkPermission:'.Permission::CHANGE_STAFF_PERMISSION)->name('staff.setpermission');
    Route::post('/staff/reset', [StaffController::class, 'resetPassword'])->middleware('checkPermission:'.Permission::RESET_STAFF_PASSWORD)->name('staff.reset');

    Route::get('/leave',[LeaveController::class,'index'])->middleware('checkPermission:'.Permission::LIST_LEAVE)->name('leave');
    Route::post('/leave/store', [LeaveController::class,'store'])->middleware('checkPermission:'.Permission::ADD_LEAVE)->name('leave.store');
    Route::get('/leave/list', [LeaveController::class,'list'])->middleware('checkPermission:'.Permission::LIST_LEAVE)->name('leave.list');
    Route::post('/leave/show', [LeaveController::class,'show'])->middleware('checkPermission:'.Permission::VIEW_LEAVE)->name('leave.show');
    Route::post('/leave/update', [LeaveController::class,'update'])->middleware('checkPermission:'.Permission::EDIT_LEAVE)->name('leave.update');
    Route::post('/leave/status', [LeaveController::class,'status'])->middleware('checkPermission:'.Permission::APPROVE_LEAVE)->name('leave.status');
    Route::post('/leave/export', [LeaveController::class, 'export'])->middleware('checkPermission:'.Permission::EXPORT_LEAVE_TO_EXCEL)->name('leave.export');
    
    Route::get('/leave/approval',[LeaveApprovalController::class,'index'])->middleware('checkPermission:'.Permission::LIST_LEAVE)->name('leave.approval');
    Route::get('/leave/approval/list',[LeaveApprovalController::class,'list'])->middleware('checkPermission:'.Permission::LIST_LEAVE)->name('leave.approval.list');
    Route::post('/leave/approval/status',[LeaveApprovalController::class,'status'])->middleware('checkPermission:'.Permission::APPROVE_LEAVE)->name('leave.approval.status');

    Route::get('/profile',[ProfileController::class,'index'])->middleware('checkPermission:'.Permission::LIST_STAFF)->name('profile');
    Route::post('/profile/update',[ProfileController::class,'update'])->middleware('checkPermission:'.Permission::EDIT_STAFF)->name('profile.update');
});

