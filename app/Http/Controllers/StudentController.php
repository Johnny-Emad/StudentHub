<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('student_code', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw(
                        "CONCAT(first_name, ' ', last_name) LIKE ?",
                        ["%{$search}%"]
                    )
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Grade Filter
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        // Class Section Filter
        if ($request->filled('class_section')) {
            $query->where('class_section', $request->class_section);
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pagination
        $students = $query->latest()
            ->paginate(10)
            ->withQueryString();

        // Dynamic Filter Options
        $grades = Student::select('grade')
            ->distinct()
            ->orderBy('grade')
            ->pluck('grade');

        $sections = Student::select('class_section')
            ->distinct()
            ->orderBy('class_section')
            ->pluck('class_section');

        return view('students.index', compact(
            'students',
            'grades',
            'sections'
        ));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_code' => 'required|string|max:255|unique:students,student_code',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:255|unique:students,email',
            'phone' => 'nullable|digits:11',
            'date_of_birth' => 'nullable|date|before_or_equal:today',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:1000',
            'grade' => 'required|string|max:50',
            'class_section' => 'required|string|size:1',
            'status' => 'required|in:active,inactive',
        ]);

        Student::create($validated);

        return redirect()->route('students.index')
            ->with('success', 'Student added successfully.');
    }

    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('students', 'student_code')->ignore($student->id),
            ],

            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',

            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($student->id),
            ],

            'phone' => 'nullable|digits:11',
            'date_of_birth' => 'nullable|date|before_or_equal:today',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:1000',
            'grade' => 'required|string|max:50',
            'class_section' => 'required|string|size:1',
            'status' => 'required|in:active,inactive',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }
}
