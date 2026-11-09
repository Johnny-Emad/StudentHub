<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExamGradeController extends Controller
{
    public function create(Exam $exam)
    {
        $students = Student::where('grade', $exam->grade)
            ->where('class_section', $exam->class_section)
            ->with('examGrades')
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

        DB::transaction(function () use ($validated, $exam) {
            foreach ($validated['grades'] as $studentId => $marks) {
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
}
