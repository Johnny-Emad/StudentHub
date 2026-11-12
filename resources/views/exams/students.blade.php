<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <a href="{{ route('admin.exams.index') }}" class="text-gray-400 hover:text-gray-600 transition">Exams</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">Target Students</span>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('admin.exams.index') }}" class="btn-secondary btn-sm">
            Back to Exams
        </a>
    </x-slot>

    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-900">Exam Target Students</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $exam->title }} — {{ $exam->subject->name }}</p>
    </div>

    {{-- Exam info card --}}
    <div class="card p-5 mb-5">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Grade</p>
                <span class="badge-blue">{{ $exam->grade }}</span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Section</p>
                <p class="text-sm font-semibold text-gray-800">{{ $exam->class_section }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Exam Date</p>
                <p class="text-sm font-semibold text-gray-800">{{ \Carbon\Carbon::parse($exam->exam_date)->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Total Marks</p>
                <p class="text-sm font-semibold text-gray-800">{{ $exam->total_marks }}</p>
            </div>
        </div>
    </div>

    {{-- Target count --}}
    <div class="mb-4 flex items-center gap-2">
        <h2 class="text-sm font-semibold text-gray-700">Target Students:</h2>
        <span class="badge-blue">{{ $targetCount }}</span>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="table-header">#</th>
                        <th class="table-header">Student Code</th>
                        <th class="table-header">First Name</th>
                        <th class="table-header">Last Name</th>
                        <th class="table-header">Email</th>
                        <th class="table-header">Grade</th>
                        <th class="table-header">Section</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="table-cell text-gray-500">{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                            <td class="table-cell">
                                <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-600">{{ $student->student_code }}</span>
                            </td>
                            <td class="table-cell font-medium text-gray-900">{{ $student->first_name }}</td>
                            <td class="table-cell font-medium text-gray-900">{{ $student->last_name }}</td>
                            <td class="table-cell text-gray-500">{{ $student->email ?? '—' }}</td>
                            <td class="table-cell"><span class="badge-blue">{{ $student->grade }}</span></td>
                            <td class="table-cell text-gray-500">{{ $student->class_section }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <p class="text-sm text-gray-500">No students found for this exam's grade and section.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
