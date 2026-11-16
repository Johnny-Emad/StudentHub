<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $dashboardStats = Cache::remember('dashboard.stats', 60, function () {
            return [
                'total' => Student::count(),
                'active' => Student::where('status', 'active')->count(),
                'inactive' => Student::where('status', 'inactive')->count(),
                'grade_levels' => Student::distinct('grade')->count('grade'),
            ];
        });

        $recentStudents = Cache::remember('dashboard.recent_students', 60, function () {
            return Student::latest()->take(5)->get();
        });

        return view('dashboard', compact('dashboardStats', 'recentStudents'));
    }
}
