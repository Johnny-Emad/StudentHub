<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('admin.academic-years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:academic_years,name'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated) {
            if (!empty($validated['is_active'])) {
                AcademicYear::query()->update(['is_active' => false]);
            }

            AcademicYear::create([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => $validated['is_active'] ?? false,
            ]);
        });

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', 'Academic year created successfully.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('academic_years', 'name')->ignore($academicYear->id),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated, $academicYear) {
            if (!empty($validated['is_active'])) {
                AcademicYear::where('id', '!=', $academicYear->id)
                    ->update(['is_active' => false]);
            }

            $academicYear->update([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => $validated['is_active'] ?? false,
            ]);
        });

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', 'Academic year updated successfully.');
    }

    public function activate(AcademicYear $academicYear)
    {
        DB::transaction(function () use ($academicYear) {
            AcademicYear::query()->update(['is_active' => false]);

            $academicYear->update(['is_active' => true]);
        });

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', 'Academic year activated successfully.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->is_active) {
            return redirect()
                ->route('admin.academic-years.index')
                ->with('error', 'You cannot delete the active academic year.');
        }

        $academicYear->delete();

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', 'Academic year deleted successfully.');
    }
}
