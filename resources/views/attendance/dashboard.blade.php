<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">Attendance Dashboard</span>
        </div>
    </x-slot>

    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-900">Attendance Dashboard</h1>
        <p class="text-sm text-gray-500 mt-0.5">Overview of attendance records for our school.</p>
    </div>

    {{-- Filters --}}
    <div class="card p-4 mb-4">
        <form method="GET" action="{{ route('attendance.dashboard') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

                <div>
                    <label class="form-label">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-input">
                </div>

                <div>
                    <label class="form-label">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-input">
                </div>

                <div>
                    <label class="form-label">Grade</label>
                    <select name="grade" class="form-input">
                        <option value="">All Grades</option>
                        @foreach ($grades as $grade)
                            <option value="{{ $grade }}" @selected(request('grade') == $grade)>
                                {{ $grade }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Section</label>
                    <select name="class_section" class="form-input">
                        <option value="">All Sections</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section }}" @selected(request('class_section') == $section)>
                                {{ $section }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary btn-sm flex-1 justify-center">Apply Filters</button>
                    <a href="{{ route('attendance.dashboard') }}" class="btn-secondary btn-sm">Reset</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Total Records</p>
            <p class="text-2xl font-bold text-gray-900 mt-1.5">{{ $total }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-medium text-emerald-600 uppercase tracking-wide">Present</p>
            <p class="text-2xl font-bold text-emerald-700 mt-1.5">{{ $present }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-medium text-red-600 uppercase tracking-wide">Absent</p>
            <p class="text-2xl font-bold text-red-700 mt-1.5">{{ $absent }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-medium text-indigo-600 uppercase tracking-wide">Attendance Rate</p>
            <p class="text-2xl font-bold text-indigo-700 mt-1.5">{{ $attendanceRate }}%</p>
        </div>
    </div>
</x-app-layout>
