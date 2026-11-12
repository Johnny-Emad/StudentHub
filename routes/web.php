
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamGradeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubjectStatisticsController;
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

            Route::patch(
                '/teachers/{user}/approve',
                [TeacherApprovalController::class, 'approve']
            )->name('teachers.approve');

            Route::patch(
                '/teachers/{user}/reject',
                [TeacherApprovalController::class, 'reject']
            )->name('teachers.reject');

            Route::get('/subjects/statistics', [
                SubjectStatisticsController::class,
                'index'
            ])->name('subjects.statistics');

            Route::resource('subjects', SubjectController::class)
                ->except(['show']);

            Route::get(
                '/subjects/{subject}/grades',
                [SubjectController::class, 'editGrades']
            )->name('subjects.grades.edit');

            Route::put(
                '/subjects/{subject}/grades',
                [SubjectController::class, 'updateGrades']
            )->name('subjects.grades.update');

            Route::get(
                '/exams/{exam}/students',
                [ExamController::class, 'targetStudents']
            )->name('exams.students');

            Route::get(
                '/exams/{exam}/grades',
                [ExamGradeController::class, 'create']
            )->name('exams.grades.create');

            Route::post(
                '/exams/{exam}/grades',
                [ExamGradeController::class, 'store']
            )->name('exams.grades.store');

            Route::get(
                '/exams/{exam}/grades/{examGrade}/edit',
                [ExamGradeController::class, 'edit']
            )->name('exams.grades.edit');

            Route::put(
                '/exams/{exam}/grades/{examGrade}',
                [ExamGradeController::class, 'update']
            )->name('exams.grades.update');

            Route::resource('exams', ExamController::class)
                ->except(['show']);
        });
});

require __DIR__ . '/auth.php';
