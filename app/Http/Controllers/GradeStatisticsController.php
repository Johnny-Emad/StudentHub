<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GradeStatisticsController extends Controller
{
    public function index()
    {
        $cachedGrades = Cache::remember('stats.grades', 300, function () {
            $grades = Student::query()
                ->select('grade')
                ->selectRaw('COUNT(*) as students_count')
                ->selectRaw('COUNT(DISTINCT class_section) as sections_count')
                ->groupBy('grade')
                ->orderBy('grade')
                ->get();

            $result = [];

            foreach ($grades as $grade) {
                $studentIds = Student::where('grade', $grade->grade)
                    ->pluck('id');

                // Subjects assigned to this grade.
                $subjectsCount = DB::table('grade_subject')
                    ->where('grade', $grade->grade)
                    ->count();

                // Exams belonging to this grade.
                $examsCount = DB::table('exams')
                    ->where('grade', $grade->grade)
                    ->count();

                // Number of recorded grades.
                $gradesCount = DB::table('exam_grades')
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

                $overallAverage = $totals->possible > 0
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

                $highestAverage = $studentAverages->isNotEmpty()
                    ? round($studentAverages->max(), 2)
                    : null;

                $lowestAverage = $studentAverages->isNotEmpty()
                    ? round($studentAverages->min(), 2)
                    : null;

                // Store plain arrays instead of Eloquent Collections.
                $result[] = [
                    'grade' => $grade->grade,
                    'students_count' => $grade->students_count,
                    'sections_count' => $grade->sections_count,
                    'subjects_count' => $subjectsCount,
                    'exams_count' => $examsCount,
                    'grades_count' => $gradesCount,
                    'overall_average' => $overallAverage,
                    'highest_average' => $highestAverage,
                    'lowest_average' => $lowestAverage,
                ];
            }

            return $result;
        });

        // Convert cached arrays into objects for the Blade view.
        $grades = collect($cachedGrades)->map(function ($grade) {
            return (object) $grade;
        });

        return view('statistics.grades', compact('grades'));
    }
}
