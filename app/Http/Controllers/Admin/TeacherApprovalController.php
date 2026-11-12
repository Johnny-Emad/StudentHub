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
        $status = request('status', 'pending');
        $validStatuses = ['pending', 'approved', 'rejected'];

        if (!in_array($status, $validStatuses)) {
            $status = 'pending';
        }

        $teachers = User::where('role', 'teacher')
            ->where('status', $status)
            ->latest()
            ->get();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function approve(User $user): RedirectResponse
    {
        abort_unless($user->role === 'teacher', 404);

        $user->update(['status' => 'approved']);

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Teacher account approved successfully.');
    }

    public function reject(User $user): RedirectResponse
    {
        abort_unless($user->role === 'teacher', 404);

        $user->update(['status' => 'rejected']);

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Teacher account rejected successfully.');
    }

    public function update(User $user, string $status): RedirectResponse
    {
        abort_unless(
            $user->role === 'teacher' &&
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
