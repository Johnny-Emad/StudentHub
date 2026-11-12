<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Facades\DB;

class GradeStatisticsController extends Controller
{
    public function index()
    {
        $grades = Student::query()
            ->select('grade')
            ->selectRaw('COUNT(*) as students_count')
            ->selectRaw('COUNT(DISTINCT class_section) as sections_count')
            ->groupBy('grade')
            ->orderBy('grade')
            ->get();

        foreach ($grades as $grade) {
            $studentIds = Student::where('grade', $grade->grade)
                ->pluck('id');

            // Subjects assigned to this grade.
            $grade->subjects_count = DB::table('grade_subject')
                ->where('grade', $grade->grade)
                ->count();

            // Exams belonging to this grade.
            $grade->exams_count = DB::table('exams')
                ->where('grade', $grade->grade)
                ->count();

            // Number of recorded grades.
            $grade->grades_count = DB::table('exam_grades')
                ->join('exams', 'exam_grades.exam_id', '=', 'exams.id')
                ->where('exams.grade', $grade->grade)
                ->whereIn('exam_grades.student_id', $studentIds)
                ->count();

            // Overall weighted average for the grade.
            $totals = DB::table('exam_grades')
                ->join('exams', 'exam_grades.exam_id', '=', 'exams.id')
                ->where('exams.grade', $grade->grade)
                ->whereIn('exam_grades.student_id', $studentIds)
                ->selectRaw('SUM(exam_grades.marks_obtained) as obtained')
                ->selectRaw('SUM(exams.total_marks) as possible')
                ->first();

            $grade->overall_average = $totals->possible > 0
                ? round(
                    ($totals->obtained / $totals->possible) * 100,
                    2
                )
                : null;

            // Calculate each student's average across recorded exams.
            $studentAverages = DB::table('exam_grades')
                ->join('exams', 'exam_grades.exam_id', '=', 'exams.id')
                ->where('exams.grade', $grade->grade)
                ->whereIn('exam_grades.student_id', $studentIds)
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

            $grade->highest_average = $studentAverages->isNotEmpty()
                ? round($studentAverages->max(), 2)
                : null;

            $grade->lowest_average = $studentAverages->isNotEmpty()
                ? round($studentAverages->min(), 2)
                : null;
        }

        return view('statistics.grades', compact('grades'));
    }
}
