<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $students = Student::query()
            ->when($request->filled('grade'), fn($query) =>
            $query->where('grade', $request->grade))
            ->when($request->filled('class_section'), fn($query) =>
            $query->where('class_section', $request->class_section))
            ->with(['attendances' => fn($query) =>
            $query->where('date', $date)])
            ->orderBy('first_name')
            ->get();

        $grades = Student::select('grade')
            ->distinct()
            ->orderBy('grade')
            ->pluck('grade');

        $sections = Student::select('class_section')
            ->distinct()
            ->orderBy('class_section')
            ->pluck('class_section');

        return view('attendance.index', compact(
            'students',
            'grades',
            'sections'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'attendance' => ['required', 'array', 'min:1'],
            'attendance.*' => ['required', Rule::in(['present', 'absent'])],
        ]);

        $studentIds = array_keys($validated['attendance']);

        if (Student::whereIn('id', $studentIds)->count() !== count($studentIds)) {
            throw ValidationException::withMessages([
                'attendance' => 'Invalid student selection.',
            ]);
        }

        DB::transaction(function () use ($validated, $request) {
            $existing = Attendance::where('date', $validated['date'])
                ->whereIn('student_id', array_keys($validated['attendance']))
                ->exists();

            if ($existing) {
                throw ValidationException::withMessages([
                    'attendance' => 'Attendance already exists for one or more selected students.',
                ]);
            }

            foreach ($validated['attendance'] as $studentId => $status) {
                Attendance::create([
                    'student_id' => $studentId,
                    'date' => $validated['date'],
                    'status' => $status,
                    'marked_by' => $request->user()->id,
                ]);
            }
        });

        return back()->with('success', 'Attendance saved successfully.');
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['present', 'absent'])],
        ]);

        $attendance->update([
            'status' => $validated['status'],
            'marked_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Attendance updated successfully.');
    }

    public function history(Request $request)
    {
        $attendances = Attendance::with(['student', 'marker'])
            ->when($request->filled('date'), fn($query) =>
            $query->whereDate('date', $request->date))
            ->when($request->filled('grade'), fn($query) =>
            $query->whereHas('student', fn($studentQuery) =>
            $studentQuery->where('grade', $request->grade)))
            ->when($request->filled('class_section'), fn($query) =>
            $query->whereHas('student', fn($studentQuery) =>
            $studentQuery->where('class_section', $request->class_section)))
            ->when($request->filled('student'), fn($query) =>
            $query->where('student_id', $request->student))
            ->latest('date')
            ->paginate(20)
            ->withQueryString();

        $grades = Student::select('grade')->distinct()->orderBy('grade')->pluck('grade');

        $sections = Student::select('class_section')
            ->distinct()->orderBy('class_section')->pluck('class_section');

        $students = Student::orderBy('first_name')->get();

        return view('attendance.history', compact(
            'attendances',
            'grades',
            'sections',
            'students'
        ));
    }
}
