<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamGrade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExamGradeController extends Controller
{
    public function create(Exam $exam)
    {
        $students = Student::where('grade', $exam->grade)
            ->where('class_section', $exam->class_section)
            ->with(['examGrades' => function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            }])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('exams.grades', compact('exam', 'students'));
    }

    public function store(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'grades' => ['required', 'array'],

            'grades.*' => [
                'required',
                'numeric',
                'min:0',
                'max:' . $exam->total_marks,
            ],
        ]);

        $studentIds = Student::where('grade', $exam->grade)
            ->where('class_section', $exam->class_section)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->all();

        foreach (array_keys($validated['grades']) as $studentId) {
            if (!in_array((string) $studentId, $studentIds, true)) {
                throw ValidationException::withMessages([
                    'grades' => 'One or more selected students do not belong to this exam.',
                ]);
            }
        }

        foreach ($validated['grades'] as $studentId => $marks) {
            $exam->grades()->updateOrCreate(
                ['student_id' => $studentId],
                ['marks_obtained' => $marks]
            );
        }

        return redirect()
            ->route('admin.exams.grades.create', $exam)
            ->with('success', 'Grades saved successfully.');
    }

    public function edit(Exam $exam, ExamGrade $examGrade)
    {
        $this->ensureGradeBelongsToExam($exam, $examGrade);

        $student = $examGrade->student;

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

        $examGrade->update($validated);

        return redirect()
            ->route('admin.exams.grades.create', $exam)
            ->with('success', 'Student grade updated successfully.');
    }

    private function ensureGradeBelongsToExam(
        Exam $exam,
        ExamGrade $examGrade
    ): void {
        abort_unless($examGrade->exam_id === $exam->id, 404);

        $student = $examGrade->student;

        abort_unless(
            $student &&
                $student->grade === $exam->grade &&
                $student->class_section === $exam->class_section,
            404
        );
    }
}
