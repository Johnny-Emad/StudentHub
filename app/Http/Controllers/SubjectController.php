<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $subjects = Subject::with('gradeAssignments')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('subjects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:subjects,name'],
            'description' => ['nullable', 'string'],
        ]);

        Subject::create($validated);

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects', 'name')->ignore($subject->id),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $subject->update($validated);

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }

    public function editGrades(Subject $subject)
    {
        $grades = Student::whereNotNull('grade')
            ->where('grade', '!=', '')
            ->distinct()
            ->orderBy('grade')
            ->pluck('grade');

        $assignedGrades = $subject->gradeAssignments()
            ->pluck('grade');

        return view('subjects.grades', compact(
            'subject',
            'grades',
            'assignedGrades'
        ));
    }

    public function updateGrades(Request $request, Subject $subject)
    {
        $availableGrades = Student::whereNotNull('grade')
            ->where('grade', '!=', '')
            ->distinct()
            ->pluck('grade')
            ->all();

        $validated = $request->validate([
            'grades' => ['nullable', 'array'],
            'grades.*' => [
                'required',
                'string',
                'distinct',
                Rule::in($availableGrades),
            ],
        ]);

        $selectedGrades = $validated['grades'] ?? [];

        DB::transaction(function () use ($subject, $selectedGrades) {
            $subject->gradeAssignments()->delete();

            foreach ($selectedGrades as $grade) {
                $subject->gradeAssignments()->create([
                    'grade' => $grade,
                ]);
            }
        });

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject grades updated successfully.');
    }
}
