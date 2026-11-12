<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <a href="{{ route('admin.exams.index') }}" class="text-gray-400 hover:text-gray-600 transition">Exams</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">Enter Grades</span>
        </div>
    </x-slot>

    {{-- Flash success --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="mb-4 flex items-center gap-2.5 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-sm font-medium text-emerald-800">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Exam info card --}}
    <div class="card p-5 mb-5">
        <h1 class="text-lg font-bold text-gray-900 mb-3">{{ $exam->title }}</h1>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Subject</p>
                <p class="text-sm font-semibold text-gray-800">{{ $exam->subject->name }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Grade</p>
                <span class="badge-blue">{{ $exam->grade }}</span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Section</p>
                <p class="text-sm font-semibold text-gray-800">{{ $exam->class_section }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Total Marks</p>
                <p class="text-sm font-semibold text-gray-800">{{ $exam->total_marks }}</p>
            </div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-3 gap-4 mb-5">
        <div class="card p-4 text-center">
            <p class="text-xs font-medium text-gray-500 mb-1">Total Students</p>
            <p class="text-2xl font-bold text-gray-900">{{ $targetCount }}</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-xs font-medium text-emerald-600 mb-1">Graded</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $gradedCount }}</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-xs font-medium text-rose-600 mb-1">Not Graded</p>
            <p class="text-2xl font-bold text-rose-600">{{ $notGradedCount }}</p>
        </div>
    </div>

    {{-- Filter form --}}
    <form method="GET" action="{{ route('admin.exams.grades.create', $exam) }}" class="mb-4 flex items-end gap-3">
        <div>
            <label class="form-label">Filter by Grade Status</label>
            <select name="status" class="form-input w-auto min-w-44">
                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Students</option>
                <option value="graded" {{ $statusFilter === 'graded' ? 'selected' : '' }}>Graded</option>
                <option value="not_graded" {{ $statusFilter === 'not_graded' ? 'selected' : '' }}>Not Graded</option>
            </select>
        </div>
        <button type="submit" class="btn-secondary btn-sm">Filter</button>
    </form>

    {{-- Grades table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="table-header">#</th>
                        <th class="table-header">Student Code</th>
                        <th class="table-header">Student Name</th>
                        <th class="table-header">Marks Obtained</th>
                        <th class="table-header">Subject Average</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($students as $student)
                        @php $existingGrade = $student->examGrades->first(); @endphp
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="table-cell text-gray-500">{{ $students->firstItem() + $loop->index }}</td>

                            <td class="table-cell">
                                <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-600">{{ $student->student_code }}</span>
                            </td>

                            <td class="table-cell font-medium text-gray-900">
                                {{ $student->first_name }} {{ $student->last_name }}
                            </td>

                            <td class="table-cell">
                                <form method="POST"
                                      action="{{ route('admin.exams.grades.store', $exam) }}"
                                      class="flex items-center gap-2">
                                    @csrf
                                    <input type="number"
                                           name="grades[{{ $student->id }}]"
                                           class="form-input w-28"
                                           min="0"
                                           max="{{ $exam->total_marks }}"
                                           step="0.01"
                                           value="{{ old('grades.' . $student->id, $existingGrade?->marks_obtained) }}"
                                           placeholder="Marks">
                                    <button type="submit" class="btn-success btn-sm">Save</button>
                                </form>
                            </td>

                            <td class="table-cell">
                                @if($student->subject_average !== null)
                                    <span class="font-semibold text-gray-900">{{ number_format($student->subject_average, 2) }}%</span>
                                @else
                                    <span class="text-gray-400 text-xs">No grades yet</span>
                                @endif
                            </td>

                            <td class="table-cell">
                                @if($existingGrade)
                                    <span class="badge-green">Graded</span>
                                @else
                                    <span class="badge-yellow">Not Graded</span>
                                @endif
                            </td>

                            <td class="table-cell">
                                @if($existingGrade)
                                    <a href="{{ route('admin.exams.grades.edit', [$exam, $existingGrade]) }}"
                                       class="btn-secondary btn-sm">Edit</a>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-sm text-gray-500">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $students->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
