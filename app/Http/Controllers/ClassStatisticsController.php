<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Facades\DB;

class ClassStatisticsController extends Controller
{
    public function index()
    {
        $classes = Student::query()
            ->select('grade', 'class_section')
            ->selectRaw('COUNT(*) as students_count')
            ->groupBy('grade', 'class_section')
            ->orderBy('grade')
            ->orderBy('class_section')
            ->get();

        foreach ($classes as $class) {
            $studentsQuery = Student::where('grade', $class->grade)
                ->where('class_section', $class->class_section);

            $studentIds = (clone $studentsQuery)->pluck('id');

            $class->subjects_count = DB::table('grade_subject')
                ->where('grade', $class->grade)
                ->count();

            $class->exams_count = DB::table('exams')
                ->where('grade', $class->grade)
                ->where('class_section', $class->class_section)
                ->count();

            $class->grades_count = DB::table('exam_grades')
                ->join('exams', 'exam_grades.exam_id', '=', 'exams.id')
                ->where('exams.grade', $class->grade)
                ->where('exams.class_section', $class->class_section)
                ->whereIn('exam_grades.student_id', $studentIds)
                ->count();

            $totals = DB::table('exam_grades')
                ->join('exams', 'exam_grades.exam_id', '=', 'exams.id')
                ->where('exams.grade', $class->grade)
                ->where('exams.class_section', $class->class_section)
                ->whereIn('exam_grades.student_id', $studentIds)
                ->selectRaw('SUM(exam_grades.marks_obtained) as obtained')
                ->selectRaw('SUM(exams.total_marks) as possible')
                ->first();

            $class->average = $totals->possible > 0
                ? round(($totals->obtained / $totals->possible) * 100, 2)
                : null;

            $studentAverages = DB::table('exam_grades')
                ->join('exams', 'exam_grades.exam_id', '=', 'exams.id')
                ->where('exams.grade', $class->grade)
                ->where('exams.class_section', $class->class_section)
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

            $class->highest_average = $studentAverages->isNotEmpty()
                ? round($studentAverages->max(), 2)
                : null;

            $class->lowest_average = $studentAverages->isNotEmpty()
                ? round($studentAverages->min(), 2)
                : null;
        }

        return view('statistics.classes', compact('classes'));
    }
}
