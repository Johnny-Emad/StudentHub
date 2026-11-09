<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\Admin\TeacherApprovalController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::resource('students', StudentController::class);

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

    Route::middleware('admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/teachers', [TeacherApprovalController::class, 'index'])
                ->name('teachers.index');

            Route::patch('/teachers/{user}/{status}', [TeacherApprovalController::class, 'update'])
                ->whereIn('status', ['approved', 'rejected'])
                ->name('teachers.update');

            Route::get('/subjects/{subject}/grades', [SubjectController::class, 'editGrades'])
                ->name('subjects.grades.edit');

            Route::put('/subjects/{subject}/grades', [SubjectController::class, 'updateGrades'])
                ->name('subjects.grades.update');

            Route::resource('subjects', SubjectController::class)
                ->except(['show']);
        });
});

require __DIR__ . '/auth.php';
