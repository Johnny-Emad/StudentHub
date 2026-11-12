<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamGrade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamGradeController extends Controller
{
    public function create(Request $request, Exam $exam)
    {
        $status = $request->query('status');

        if (!in_array($status, ['graded', 'not_graded', null], true)) {
            $status = null;
        }

        $baseQuery = Student::query()
            ->where('grade', $exam->grade)
            ->where('class_section', $exam->class_section);

        $gradedCount = (clone $baseQuery)
            ->whereHas('examGrades', function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            })
            ->count();

        $totalStudents = (clone $baseQuery)->count();

        $notGradedCount = $totalStudents - $gradedCount;

        $studentsQuery = Student::query()
            ->where('grade', $exam->grade)
            ->where('class_section', $exam->class_section);

        if ($status === 'graded') {
            $studentsQuery->whereHas('examGrades', function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            });
        } elseif ($status === 'not_graded') {
            $studentsQuery->whereDoesntHave('examGrades', function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            });
        }

        $students = $studentsQuery
            ->with([
                'examGrades' => function ($query) use ($exam) {
                    $query->where('exam_id', $exam->id);
                }
            ])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(20)
            ->withQueryString();

        return view('exams.grades', compact(
            'exam',
            'students',
            'totalStudents',
            'gradedCount',
            'notGradedCount',
            'status'
        ));
    }

    public function store(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*' => [
                'nullable',
                'numeric',
                'min:0',
                'max:' . $exam->total_marks,
            ],
        ]);

        $grades = array_filter(
            $validated['grades'],
            fn($value) => $value !== null && $value !== ''
        );

        $studentIds = array_keys($grades);

        $validStudents = Student::query()
            ->whereIn('id', $studentIds)
            ->where('grade', $exam->grade)
            ->where('class_section', $exam->class_section)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->all();

        $invalidStudents = array_diff(
            array_map('strval', $studentIds),
            $validStudents
        );

        if (!empty($invalidStudents)) {
            return back()->withErrors([
                'grades' => 'One or more students do not belong to this exam.',
            ])->withInput();
        }

        DB::transaction(function () use ($exam, $grades) {
            foreach ($grades as $studentId => $marks) {
                $exam->grades()->updateOrCreate(
                    ['student_id' => $studentId],
                    ['marks_obtained' => $marks]
                );
            }
        });

        return redirect()
            ->route('admin.exams.grades.create', $exam)
            ->with('success', 'Grades saved successfully.');
    }

    public function edit(Exam $exam, ExamGrade $examGrade)
    {
        $student = $this->ensureGradeBelongsToExam($exam, $examGrade);

        return view('exams.grade-edit', compact(
            'exam',
            'examGrade',
            'student'
        ));
    }

    public function update(
        Request $request,
        Exam $exam,
        ExamGrade $examGrade
    ) {
        $this->ensureGradeBelongsToExam($exam, $examGrade);

        $validated = $request->validate([
            'marks_obtained' => [
                'required',
                'numeric',
                'min:0',
                'max:' . $exam->total_marks,
            ],
        ]);

        $examGrade->update([
            'marks_obtained' => $validated['marks_obtained'],
        ]);

        return redirect()
            ->route('admin.exams.grades.create', $exam)
            ->with('success', 'Student grade updated successfully.');
    }

    private function ensureGradeBelongsToExam(
        Exam $exam,
        ExamGrade $examGrade
    ): Student {
        abort_unless(
            (int) $examGrade->exam_id === (int) $exam->id,
            404
        );

        $student = Student::findOrFail($examGrade->student_id);

        abort_unless(
            $student->grade === $exam->grade &&
                $student->class_section === $exam->class_section,
            404
        );

        return $student;
    }
}
