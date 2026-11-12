<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolStatisticsController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;

        // General school statistics
        $studentsCount = Student::count();

        $gradesCount = Student::distinct('grade')
            ->count('grade');

        $sectionsCount = Student::query()
            ->select('grade', 'class_section')
            ->distinct()
            ->count();

        $subjectsCount = DB::table('subjects')->count();

        // Exams within selected date range
        $examsQuery = DB::table('exams');

        if ($from) {
            $examsQuery->whereDate('exam_date', '>=', $from);
        }

        if ($to) {
            $examsQuery->whereDate('exam_date', '<=', $to);
        }

        $examsCount = $examsQuery->count();

        // Grades within selected exam date range
        $gradesQuery = DB::table('exam_grades')
            ->join('exams', 'exam_grades.exam_id', '=', 'exams.id');

        if ($from) {
            $gradesQuery->whereDate('exams.exam_date', '>=', $from);
        }

        if ($to) {
            $gradesQuery->whereDate('exams.exam_date', '<=', $to);
        }

        $gradesCountRecorded = (clone $gradesQuery)->count();

        $totals = (clone $gradesQuery)
            ->selectRaw('SUM(exam_grades.marks_obtained) as obtained')
            ->selectRaw('SUM(exams.total_marks) as possible')
            ->first();

        $overallAverage = $totals->possible > 0
            ? round(($totals->obtained / $totals->possible) * 100, 2)
            : null;

        // Student averages within selected period
        $studentAverages = (clone $gradesQuery)
            ->select('exam_grades.student_id')
            ->selectRaw('SUM(exam_grades.marks_obtained) as obtained')
            ->selectRaw('SUM(exams.total_marks) as possible')
            ->groupBy('exam_grades.student_id')
            ->get()
            ->map(function ($record) {
                return $record->possible > 0
                    ? ($record->obtained / $record->possible) * 100
                    : null;
            })
            ->filter(fn($average) => $average !== null);

        $highestAverage = $studentAverages->isNotEmpty()
            ? round($studentAverages->max(), 2)
            : null;

        $lowestAverage = $studentAverages->isNotEmpty()
            ? round($studentAverages->min(), 2)
            : null;

        // Attendance within selected date range
        $attendanceQuery = DB::table('attendances');

        if ($from) {
            $attendanceQuery->whereDate('date', '>=', $from);
        }

        if ($to) {
            $attendanceQuery->whereDate('date', '<=', $to);
        }

        $attendanceCount = (clone $attendanceQuery)->count();

        $presentCount = (clone $attendanceQuery)
            ->where('status', 'present')
            ->count();

        $absentCount = (clone $attendanceQuery)
            ->where('status', 'absent')
            ->count();

        $attendanceRate = $attendanceCount > 0
            ? round(($presentCount / $attendanceCount) * 100, 2)
            : null;

        return view('statistics.school', compact(
            'studentsCount',
            'gradesCount',
            'sectionsCount',
            'subjectsCount',
            'examsCount',
            'gradesCountRecorded',
            'overallAverage',
            'highestAverage',
            'lowestAverage',
            'attendanceCount',
            'presentCount',
            'absentCount',
            'attendanceRate',
            'from',
            'to'
        ));
    }
}
