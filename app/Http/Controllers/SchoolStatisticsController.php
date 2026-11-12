
<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Facades\DB;

class SchoolStatisticsController extends Controller
{
    public function index()
    {
        // Students statistics
        $studentsCount = Student::count();

        $gradesCount = Student::distinct('grade')
            ->count('grade');

        $sectionsCount = Student::query()
            ->select('grade', 'class_section')
            ->distinct()
            ->count();

        // Subjects and exams
        $subjectsCount = DB::table('subjects')->count();

        $examsCount = DB::table('exams')->count();

        // Recorded grades
        $gradesCountRecorded = DB::table('exam_grades')->count();

        // Overall academic average
        $totals = DB::table('exam_grades')
            ->join('exams', 'exam_grades.exam_id', '=', 'exams.id')
            ->selectRaw('SUM(exam_grades.marks_obtained) as obtained')
            ->selectRaw('SUM(exams.total_marks) as possible')
            ->first();

        $overallAverage = $totals->possible > 0
            ? round(($totals->obtained / $totals->possible) * 100, 2)
            : null;

        // Student averages
        $studentAverages = DB::table('exam_grades')
            ->join('exams', 'exam_grades.exam_id', '=', 'exams.id')
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

        // Attendance statistics
        $attendanceCount = DB::table('attendances')->count();

        $presentCount = DB::table('attendances')
            ->where('status', 'present')
            ->count();

        $absentCount = DB::table('attendances')
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
            'attendanceRate'
        ));
    }
}
