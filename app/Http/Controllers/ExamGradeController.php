
<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamGrade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExamGradeController extends Controller
{
    public function create(Request $request, Exam $exam)
    {
        $targetStudents = Student::where('grade', $exam->grade)
            ->where('class_section', $exam->class_section);

        $targetCount = (clone $targetStudents)->count();

        $gradedCount = ExamGrade::where('exam_id', $exam->id)
            ->whereHas('student', function ($query) use ($exam) {
                $query->where('grade', $exam->grade)
                    ->where('class_section', $exam->class_section);
            })
            ->count();

        $notGradedCount = $targetCount - $gradedCount;

        $statusFilter = $request->query('status', 'all');

        $studentsQuery = Student::where('grade', $exam->grade)
            ->where('class_section', $exam->class_section)
            ->with([
                'examGrades' => function ($query) use ($exam) {
                    $query->where('exam_id', $exam->id);
                }
            ])
            ->orderBy('first_name')
            ->orderBy('last_name');

        if ($statusFilter === 'graded') {
            $studentsQuery->whereHas('examGrades', function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            });
        } elseif ($statusFilter === 'not_graded') {
            $studentsQuery->whereDoesntHave('examGrades', function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            });
        } else {
            $statusFilter = 'all';
        }

        $students = $studentsQuery->paginate(20)->withQueryString();

        // Calculate each student's weighted average in this subject.
        $studentIds = $students->getCollection()->pluck('id');

        $averages = DB::table('exam_grades')
            ->join('exams', 'exam_grades.exam_id', '=', 'exams.id')
            ->where('exams.subject_id', $exam->subject_id)
            ->where('exams.grade', $exam->grade)
            ->where('exams.class_section', $exam->class_section)
            ->whereIn('exam_grades.student_id', $studentIds)
            ->select('exam_grades.student_id')
            ->selectRaw('SUM(exam_grades.marks_obtained) as total_obtained')
            ->selectRaw('SUM(exams.total_marks) as total_possible')
            ->groupBy('exam_grades.student_id')
            ->get()
            ->keyBy('student_id');

        foreach ($students as $student) {
            $averageData = $averages->get($student->id);

            $student->subject_average = $averageData
                ? round(
                    ($averageData->total_obtained / $averageData->total_possible) * 100,
                    2
                )
                : null;
        }

        return view('exams.grades', compact(
            'exam',
            'students',
            'targetCount',
            'gradedCount',
            'notGradedCount',
            'statusFilter'
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

        $studentIds = array_keys($validated['grades']);

        $validStudentIds = Student::where('grade', $exam->grade)
            ->where('class_section', $exam->class_section)
            ->whereIn('id', $studentIds)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->all();

        foreach ($validated['grades'] as $studentId => $marks) {
            if (!in_array((string) $studentId, $validStudentIds, true)) {
                throw ValidationException::withMessages([
                    'grades' => 'One or more selected students do not belong to this exam.',
                ]);
            }

            if ($marks === null || $marks === '') {
                continue;
            }

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
