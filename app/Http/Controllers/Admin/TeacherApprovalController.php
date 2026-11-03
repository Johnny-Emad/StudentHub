<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeacherApprovalController extends Controller
{
    public function index(): View
    {
        $teachers = User::where('role', 'teacher')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function update(User $user, string $status): RedirectResponse
    {
        abort_unless(
            $user->role === 'teacher' &&
                $user->status === 'pending' &&
                in_array($status, ['approved', 'rejected'], true),
            404
        );

        $user->update([
            'status' => $status,
        ]);

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', "Teacher account {$status} successfully.");
    }
}
