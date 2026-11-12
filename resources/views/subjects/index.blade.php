<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">Subjects</span>
        </div>
    </x-slot>

    <div class="mb-5 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Subjects</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage subjects and their assigned grades.</p>
        </div>
        <a href="{{ route('admin.subjects.create') }}" class="btn-primary btn-sm self-start">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Subject
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-2.5 px-4 py-3 rounded-md bg-emerald-50 border border-emerald-200 text-sm font-medium text-emerald-800">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Search --}}
    <div class="card p-4 mb-4">
        <form method="GET" action="{{ route('admin.subjects.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search subjects..."
                class="form-input sm:max-w-sm"
            >
            <button type="submit" class="btn-primary btn-sm">Search</button>
            <a href="{{ route('admin.subjects.index') }}" class="btn-secondary btn-sm">Reset</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="table-header w-12">#</th>
                        <th class="table-header">Subject</th>
                        <th class="table-header">Description</th>
                        <th class="table-header">Assigned Grades</th>
                        <th class="table-header text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($subjects as $subject)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="table-cell text-gray-400">
                                {{ $subjects->firstItem() + $loop->index }}
                            </td>
                            <td class="table-cell font-semibold text-gray-900">
                                {{ $subject->name }}
                            </td>
                            <td class="table-cell text-gray-500 max-w-xs truncate">
                                {{ $subject->description ?: 'No description' }}
                            </td>
                            <td class="table-cell">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($subject->gradeAssignments as $assignment)
                                        <span class="badge badge-gray">{{ $assignment->grade }}</span>
                                    @empty
                                        <span class="text-xs text-gray-400">No grades assigned</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="table-cell">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.subjects.grades.edit', $subject) }}"
                                       class="px-2.5 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50 rounded-md transition">
                                        Grades
                                    </a>
                                    <a href="{{ route('admin.subjects.edit', $subject) }}"
                                       class="px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-md transition">
                                        Edit
                                    </a>
                                    <form
                                        action="{{ route('admin.subjects.destroy', $subject) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this subject?')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded-md transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                </svg>
                                <p class="text-sm font-medium text-gray-500">No subjects found</p>
                                <p class="text-xs text-gray-400 mt-1">Add your first subject to get started</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subjects->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $subjects->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
