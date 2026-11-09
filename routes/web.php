<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamGradeController;
use App\Http\Controllers\SubjectController;

use App\Http\Controllers\Admin\TeacherApprovalController;

Route::get('/', function () {
    return view('welcome');
});

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

    // Students
    Route::resource('students', StudentController::class);

    // Attendance
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

    // Admin
    Route::middleware('admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Teacher Approval
            Route::get('/teachers', [TeacherApprovalController::class, 'index'])
                ->name('teachers.index');

            Route::patch('/teachers/{user}/status', [TeacherApprovalController::class, 'update'])
                ->name('teachers.update');

            // Subjects
            Route::resource('subjects', SubjectController::class)
                ->except(['show']);

            Route::get('/subjects/{subject}/grades', [SubjectController::class, 'editGrades'])
                ->name('subjects.grades.edit');

            Route::put('/subjects/{subject}/grades', [SubjectController::class, 'updateGrades'])
                ->name('subjects.grades.update');

            // Exams
            Route::get('/exams/{exam}/students', [ExamController::class, 'targetStudents'])
                ->name('exams.students');

            Route::get('/exams/{exam}/grades', [ExamGradeController::class, 'create'])
                ->name('exams.grades.create');

            Route::post('/exams/{exam}/grades', [ExamGradeController::class, 'store'])
                ->name('exams.grades.store');

            Route::resource('exams', ExamController::class)
                ->except(['show']);
        });
});

require __DIR__ . '/auth.php';
