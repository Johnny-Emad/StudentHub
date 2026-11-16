<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">Attendance</span>
        </div>
    </x-slot>

    {{-- Page heading --}}
    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-900">Attendance</h1>
        <p class="text-sm text-gray-500 mt-0.5">Mark and review daily attendance records.</p>
    </div>

    {{-- Flash success --}}
    @if (session('success'))
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

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="mb-4 flex items-start gap-2.5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-800">
            <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
            <ul class="space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card p-4 mb-4">
        <form method="GET" action="{{ route('attendance.index') }}">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-36">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" name="date" id="date"
                           value="{{ request('date', now()->toDateString()) }}"
                           class="form-input">
                </div>

                <div class="flex-1 min-w-32">
                    <label for="grade" class="form-label">Grade</label>
                    <select name="grade" id="grade" class="form-input">
                        <option value="">All Grades</option>
                        @foreach ($grades as $grade)
                            <option value="{{ $grade }}" @selected(request('grade') == $grade)>
                                Grade {{ $grade }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1 min-w-36">
                    <label for="class_section" class="form-label">Class</label>
                    <select name="class_section" id="class_section" class="form-input">
                        <option value="">All Classes</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section }}" @selected(request('class_section') == $section)>
                                Class {{ $section }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn-primary btn-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('attendance.index') }}" class="btn-secondary btn-sm">Reset</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Attendance table --}}
    <form method="POST" action="{{ route('attendance.store') }}">
        @csrf
        <input type="hidden" name="date" value="{{ request('date', now()->toDateString()) }}">

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="table-header">Student</th>
                            <th class="table-header">Code</th>
                            <th class="table-header">Grade</th>
                            <th class="table-header">Class</th>
                            <th class="table-header">Status</th>
                            <th class="table-header text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($students as $student)
                            @php $attendance = $student->attendances->first(); @endphp
                            <tr class="hover:bg-gray-50/50 transition">

                                {{-- Student --}}
                                <td class="table-cell">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                            <span class="text-xs font-bold text-indigo-700">
                                                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </p>
                                    </div>
                                </td>

                                <td class="table-cell">
                                    <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-600">
                                        {{ $student->student_code }}
                                    </span>
                                </td>

                                <td class="table-cell">
                                    <span class="badge-blue">{{ $student->grade }}</span>
                                </td>

                                <td class="table-cell text-gray-500">{{ $student->class_section }}</td>

                                {{-- Current status --}}
                                <td class="table-cell">
                                    @if ($attendance)
                                        @if ($attendance->status === 'present')
                                            <span class="badge-green">Present</span>
                                        @else
                                            <span class="badge-red">Absent</span>
                                        @endif
                                    @else
                                        <select name="attendance[{{ $student->id }}]"
                                                class="form-input py-1.5 text-xs w-32">
                                            <option value="present">Present</option>
                                            <option value="absent">Absent</option>
                                        </select>
                                    @endif
                                </td>

                                {{-- Action --}}
                                <td class="table-cell">
                                    <div class="flex justify-end">
                                        @if ($attendance)
                                            <form method="POST"
                                                  action="{{ route('attendance.update', $attendance) }}"
                                                  class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status"
                                                        class="form-input py-1.5 text-xs w-28">
                                                    <option value="present" @selected($attendance->status === 'present')>Present</option>
                                                    <option value="absent"  @selected($attendance->status === 'absent')>Absent</option>
                                                </select>
                                                <button type="submit" class="btn-primary btn-sm">
                                                    Update
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Not recorded</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-500">No students found</p>
                                    <p class="text-xs text-gray-400 mt-1">Try adjusting the filters above.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Save button row --}}
            @if ($students->contains(fn ($student) => !$student->attendances->isNotEmpty()))
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-sm text-gray-500">{{ $students->count() }} student(s) listed</p>
                    <button type="submit" class="btn-primary btn-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Save Attendance
                    </button>
                </div>
            @endif
        </div>
    </form>

</x-app-layout>
