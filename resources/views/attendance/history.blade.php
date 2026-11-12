<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">Attendance History</span>
        </div>
    </x-slot>

    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-900">Attendance History</h1>
        <p class="text-sm text-gray-500 mt-0.5">Review recorded attendance across classes and dates.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 flex items-center gap-2.5 px-4 py-3 rounded-md bg-emerald-50 border border-emerald-200 text-sm font-medium text-emerald-800">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="card p-4 mb-4">
        <form method="GET" action="{{ route('attendance.history') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">

                <div>
                    <label for="from" class="form-label">From</label>
                    <input type="date" id="from" name="from" value="{{ request('from') }}" class="form-input">
                </div>

                <div>
                    <label for="to" class="form-label">To</label>
                    <input type="date" id="to" name="to" value="{{ request('to') }}" min="{{ request('from') }}" class="form-input">
                </div>

                <div>
                    <label for="grade" class="form-label">Grade</label>
                    <select id="grade" name="grade" class="form-input">
                        <option value="">All Grades</option>
                        @foreach ($grades as $grade)
                            <option value="{{ $grade }}" {{ request('grade') == $grade ? 'selected' : '' }}>
                                {{ $grade }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="class_section" class="form-label">Class Section</label>
                    <select id="class_section" name="class_section" class="form-input">
                        <option value="">All Sections</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section }}" {{ request('class_section') == $section ? 'selected' : '' }}>
                                {{ $section }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="student" class="form-label">Student</label>
                    <select id="student" name="student" class="form-input">
                        <option value="">All Students</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" {{ request('student') == $student->id ? 'selected' : '' }}>
                                {{ $student->first_name }} {{ $student->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary btn-sm flex-1 justify-center">Filter</button>
                    <a href="{{ route('attendance.history') }}" class="btn-secondary btn-sm">Reset</a>
                </div>
            </div>
        </form>
    </div>

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-md bg-red-50 border border-red-200 text-sm font-medium text-red-800">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="table-header">Date</th>
                        <th class="table-header">Student</th>
                        <th class="table-header">Grade</th>
                        <th class="table-header">Section</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Recorded By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($attendances as $attendance)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="table-cell whitespace-nowrap text-gray-900 font-medium">
                                {{ $attendance->date }}
                            </td>
                            <td class="table-cell whitespace-nowrap">
                                @if ($attendance->student)
                                    {{ $attendance->student->first_name }} {{ $attendance->student->last_name }}
                                @else
                                    <span class="text-gray-400">Unknown Student</span>
                                @endif
                            </td>
                            <td class="table-cell whitespace-nowrap text-gray-500">
                                {{ $attendance->student->grade ?? '-' }}
                            </td>
                            <td class="table-cell whitespace-nowrap text-gray-500">
                                {{ $attendance->student->class_section ?? '-' }}
                            </td>
                            <td class="table-cell whitespace-nowrap">
                                @php
                                    $statusBadge = match(strtolower($attendance->status)) {
                                        'present' => 'badge-green',
                                        'absent'  => 'badge-red',
                                        'late'    => 'badge-yellow',
                                        default   => 'badge-gray',
                                    };
                                @endphp
                                <span class="{{ $statusBadge }}">{{ ucfirst($attendance->status) }}</span>
                            </td>
                            <td class="table-cell whitespace-nowrap text-gray-500">
                                {{ $attendance->marker->name ?? 'Unknown' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                </svg>
                                <p class="text-sm font-medium text-gray-500">No attendance records found</p>
                                <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $attendances->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
