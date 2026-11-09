<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

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
}
