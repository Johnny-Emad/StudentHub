<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // Dashboard statistics
        $dashboardStats = Cache::remember('dashboard.stats', 60, function () {
            return [
                'total' => Student::count(),
                'active' => Student::where('status', 'active')->count(),
                'inactive' => Student::where('status', 'inactive')->count(),
                'grade_levels' => Student::distinct('grade')->count('grade'),
            ];
        });

        // Cache recent student IDs instead of Eloquent Collection
        $recentStudentIds = Cache::remember('dashboard.recent_student_ids', 60, function () {
            return Student::latest()
                ->take(5)
                ->pluck('id')
                ->toArray();
        });

        // Retrieve students from database
        $recentStudents = Student::whereIn('id', $recentStudentIds)
            ->latest()
            ->get();

        return view('dashboard', compact('dashboardStats', 'recentStudents'));
    }
}
