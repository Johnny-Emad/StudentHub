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
            'sections',
            'date'
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
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'grade' => ['nullable', 'string'],
            'class_section' => ['nullable', 'string'],
            'student' => ['nullable', 'integer', 'exists:students,id'],
        ]);

        $attendances = Attendance::with(['student', 'marker'])
            ->when(!empty($validated['from']), fn($query) =>
            $query->whereDate('date', '>=', $validated['from']))
            ->when(!empty($validated['to']), fn($query) =>
            $query->whereDate('date', '<=', $validated['to']))
            ->when(!empty($validated['grade']), fn($query) =>
            $query->whereHas('student', fn($studentQuery) =>
            $studentQuery->where('grade', $validated['grade'])))
            ->when(!empty($validated['class_section']), fn($query) =>
            $query->whereHas('student', fn($studentQuery) =>
            $studentQuery->where('class_section', $validated['class_section'])))
            ->when(!empty($validated['student']), fn($query) =>
            $query->where('student_id', $validated['student']))
            ->latest('date')
            ->paginate(20)
            ->withQueryString();

        $grades = Student::select('grade')
            ->distinct()
            ->orderBy('grade')
            ->pluck('grade');

        $sections = Student::select('class_section')
            ->distinct()
            ->orderBy('class_section')
            ->pluck('class_section');

        $students = Student::orderBy('first_name')->get();

        return view('attendance.history', compact(
            'attendances',
            'grades',
            'sections',
            'students'
        ));
    }

    public function dashboard(Request $request)
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'grade' => ['nullable', 'string'],
            'class_section' => ['nullable', 'string'],
        ]);

        $query = Attendance::query()
            ->whereHas('student', function ($studentQuery) use ($request) {
                $studentQuery
                    ->when($request->filled('grade'), fn($q) =>
                    $q->where('grade', $request->grade))
                    ->when($request->filled('class_section'), fn($q) =>
                    $q->where('class_section', $request->class_section));
            })
            ->when($request->filled('from'), fn($q) =>
            $q->whereDate('date', '>=', $request->from))
            ->when($request->filled('to'), fn($q) =>
            $q->whereDate('date', '<=', $request->to));

        $total = (clone $query)->count();
        $present = (clone $query)->where('status', 'present')->count();
        $absent = (clone $query)->where('status', 'absent')->count();

        $attendanceRate = $total > 0
            ? round(($present / $total) * 100, 2)
            : 0;

        $grades = Student::select('grade')
            ->distinct()
            ->orderBy('grade')
            ->pluck('grade');

        $sections = Student::select('class_section')
            ->distinct()
            ->orderBy('class_section')
            ->pluck('class_section');

        return view('attendance.dashboard', compact(
            'total',
            'present',
            'absent',
            'attendanceRate',
            'grades',
            'sections'
        ));
    }
}
