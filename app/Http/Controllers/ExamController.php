<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with('subject')
            ->latest('exam_date')
            ->paginate(15);

        return view('exams.index', compact('exams'));
    }

    public function create()
    {
        $subjects = Subject::with('gradeAssignments')
            ->orderBy('name')
            ->get();

        return view('exams.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateExam($request);

        Exam::create($validated);

        return redirect()
            ->route('admin.exams.index')
            ->with('success', 'Exam created successfully.');
    }

    public function edit(Exam $exam)
    {
        $subjects = Subject::with('gradeAssignments')
            ->orderBy('name')
            ->get();

        return view('exams.edit', compact('exam', 'subjects'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $this->validateExam($request);

        $exam->update($validated);

        return redirect()
            ->route('admin.exams.index')
            ->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()
            ->route('admin.exams.index')
            ->with('success', 'Exam deleted successfully.');
    }

    public function targetStudents(Exam $exam)
    {
        $students = Student::where('grade', $exam->grade)
            ->where('class_section', $exam->class_section)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(20);

        $targetCount = Student::where('grade', $exam->grade)
            ->where('class_section', $exam->class_section)
            ->count();

        return view('exams.students', compact(
            'exam',
            'students',
            'targetCount'
        ));
    }

    private function validateExam(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],

            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],

            'grade' => [
                'required',
                'string',
                Rule::exists('grade_subject', 'grade')
                    ->where('subject_id', $request->input('subject_id')),
            ],

            'class_section' => [
                'required',
                'string',
                'max:10',
                Rule::exists('students', 'class_section')
                    ->where('grade', $request->input('grade')),
            ],

            'exam_date' => ['required', 'date'],

            'total_marks' => [
                'required',
                'numeric',
                'gt:0',
                'max:999999.99',
            ],
        ]);
    }
}
