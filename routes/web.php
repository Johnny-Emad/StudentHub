<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ExamController;

use App\Http\Controllers\Admin\TeacherApprovalController;
use App\Http\Controllers\SubjectController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    // Students Management
    Route::resource('students', StudentController::class);


    // Attendance Management
    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::post('/attendance', [AttendanceController::class, 'store'])
        ->name('attendance.store');

    Route::patch('/attendance/{attendance}', [AttendanceController::class, 'update'])
        ->name('attendance.update');

    Route::get('/attendance/history', [AttendanceController::class, 'history'])
        ->name('attendance.history');

    Route::get('/attendance/dashboard', [AttendanceController::class, 'dashboard'])
        ->name('attendance.dashboard');


    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Teacher Approval
            Route::get('/teachers', [TeacherApprovalController::class, 'index'])
                ->name('teachers.index');

            Route::patch('/teachers/{user}/status', [TeacherApprovalController::class, 'update'])
                ->whereIn('status', ['approved', 'rejected'])
                ->name('teachers.update');


            // Subjects Management
            Route::resource('subjects', SubjectController::class)
                ->except(['show']);

            // Subject Grade Assignment
            Route::get('/subjects/{subject}/grades', [SubjectController::class, 'editGrades'])
                ->name('subjects.grades.edit');

            Route::put('/subjects/{subject}/grades', [SubjectController::class, 'updateGrades'])
                ->name('subjects.grades.update');


            // Exams Management
            Route::resource('exams', ExamController::class)
                ->except(['show']);
        });
});


require __DIR__ . '/auth.php';
