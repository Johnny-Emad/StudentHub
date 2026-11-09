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
        $students = Student::query()
            ->when($request->filled('grade'), function ($query) use ($request) {
                $query->where('grade', $request->grade);
            })
            ->when($request->filled('class_section'), function ($query) use ($request) {
                $query->where('class_section', $request->class_section);
            })
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

        $students = Student::whereIn('id', $studentIds)->pluck('id')->all();

        if (count($students) !== count($studentIds)) {
            throw ValidationException::withMessages([
                'attendance' => 'Invalid student selection.',
            ]);
        }

        DB::transaction(function () use ($validated, $request) {

            $existingAttendance = Attendance::where('date', $validated['date'])
                ->whereIn('student_id', array_keys($validated['attendance']))
                ->exists();

            if ($existingAttendance) {
                throw ValidationException::withMessages([
                    'attendance' => 'Attendance has already been recorded for one or more selected students on this date.',
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

        return redirect()
            ->route('attendance.index')
            ->with('success', 'Attendance saved successfully.');
    }
}
