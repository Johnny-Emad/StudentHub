<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class SubjectStatisticsController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount('exams')
            ->orderBy('name')
            ->get();

        foreach ($subjects as $subject) {
            $examsQuery = DB::table('exams')
                ->where('subject_id', $subject->id);

            $subject->targeted_students = DB::table('exams')
                ->join('students', function ($join) {
                    $join->on('students.grade', '=', 'exams.grade')
                        ->on('students.class_section', '=', 'exams.class_section');
                })
                ->where('exams.subject_id', $subject->id)
                ->distinct()
                ->count('students.id');

            $subject->graded_count = DB::table('exam_grades')
                ->join('exams', 'exam_grades.exam_id', '=', 'exams.id')
                ->where('exams.subject_id', $subject->id)
                ->count();

            $totals = DB::table('exam_grades')
                ->join('exams', 'exam_grades.exam_id', '=', 'exams.id')
                ->where('exams.subject_id', $subject->id)
                ->selectRaw('SUM(exam_grades.marks_obtained) as obtained')
                ->selectRaw('SUM(exams.total_marks) as possible')
                ->first();

            $subject->overall_average = $totals->possible > 0
                ? round(($totals->obtained / $totals->possible) * 100, 2)
                : null;

            $studentAverages = DB::table('exam_grades')
                ->join('exams', 'exam_grades.exam_id', '=', 'exams.id')
                ->where('exams.subject_id', $subject->id)
                ->select('exam_grades.student_id')
                ->selectRaw(
                    'SUM(exam_grades.marks_obtained) as obtained'
                )
                ->selectRaw(
                    'SUM(exams.total_marks) as possible'
                )
                ->groupBy('exam_grades.student_id')
                ->get()
                ->map(function ($record) {
                    return $record->possible > 0
                        ? ($record->obtained / $record->possible) * 100
                        : null;
                })
                ->filter(fn($average) => $average !== null);

            $subject->highest_average = $studentAverages->isNotEmpty()
                ? round($studentAverages->max(), 2)
                : null;

            $subject->lowest_average = $studentAverages->isNotEmpty()
                ? round($studentAverages->min(), 2)
                : null;
        }

        return view('subjects.statistics', compact('subjects'));
    }
}
