<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">School Statistics</span>
        </div>
    </x-slot>

    {{-- Page heading --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">School Statistics</h1>
        <p class="text-sm text-gray-500 mt-0.5">School-wide overview for the selected date range.</p>
    </div>

    {{-- Date filter --}}
    <div class="card p-5 mb-6">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">Filter by Date Range</h2>
        <form method="GET" action="{{ route('admin.statistics.school') }}">
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-36">
                    <label for="from" class="form-label">From</label>
                    <input type="date" name="from" id="from" value="{{ $from }}" class="form-input">
                </div>
                <div class="flex-1 min-w-36">
                    <label for="to" class="form-label">To</label>
                    <input type="date" name="to" id="to" value="{{ $to }}" min="{{ $from }}" class="form-input">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary btn-sm">Apply</button>
                    <a href="{{ route('admin.statistics.school') }}" class="btn-secondary btn-sm">Reset</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Students Overview --}}
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Students Overview</p>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @php
            $studentCards = [
                ['label' => 'Total Students',  'value' => $studentsCount,  'color' => 'indigo',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>'],
                ['label' => 'Total Grades',    'value' => $gradesCount,    'color' => 'violet',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>'],
                ['label' => 'Total Classes',  'value' => $sectionsCount,  'color' => 'sky',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>'],
            ];
            $colorMap = [
                'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
                'violet' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600'],
                'sky'    => ['bg' => 'bg-sky-50',    'text' => 'text-sky-600'],
                'emerald'=> ['bg' => 'bg-emerald-50','text' => 'text-emerald-600'],
                'amber'  => ['bg' => 'bg-amber-50',  'text' => 'text-amber-600'],
                'rose'   => ['bg' => 'bg-rose-50',   'text' => 'text-rose-600'],
                'blue'   => ['bg' => 'bg-blue-50',   'text' => 'text-blue-600'],
            ];
        @endphp
        @foreach($studentCards as $card)
        @php $c = $colorMap[$card['color']]; @endphp
        <div class="card p-5">
            <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} flex items-center justify-center mb-4">
                <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    {!! $card['svg'] !!}
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
            <p class="text-sm text-gray-500 mt-0.5">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Academic Overview --}}
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Academic Overview</p>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @php
            $academicCards = [
                ['label' => 'Total Subjects',         'value' => $subjectsCount,          'color' => 'rose',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>'],
                ['label' => 'Exams in Period',        'value' => $examsCount,             'color' => 'violet',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 8.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v8.25A2.25 2.25 0 006 16.5h2.25m8.25-8.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-7.5A2.25 2.25 0 018.25 18v-1.5m8.25-8.25h-6a2.25 2.25 0 00-2.25 2.25v6"/>'],
                ['label' => 'Recorded Grades',        'value' => $gradesCountRecorded,    'color' => 'blue',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>'],
                ['label' => 'Overall Average',        'value' => $overallAverage !== null ? $overallAverage . '%' : 'N/A',  'color' => 'indigo',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>'],
                ['label' => 'Highest Student Avg',    'value' => $highestAverage !== null ? $highestAverage . '%' : 'N/A',  'color' => 'emerald',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>'],
                ['label' => 'Lowest Student Avg',     'value' => $lowestAverage !== null ? $lowestAverage . '%' : 'N/A',   'color' => 'rose',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181"/>'],
            ];
        @endphp
        @foreach($academicCards as $card)
        @php $c = $colorMap[$card['color']]; @endphp
        <div class="card p-5">
            <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} flex items-center justify-center mb-4">
                <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    {!! $card['svg'] !!}
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
            <p class="text-sm text-gray-500 mt-0.5">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Attendance Overview --}}
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Attendance Overview</p>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $attendanceCards = [
                ['label' => 'Total Records',    'value' => $attendanceCount,                                             'color' => 'indigo',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>'],
                ['label' => 'Present Records',  'value' => $presentCount,                                               'color' => 'emerald',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                ['label' => 'Absent Records',   'value' => $absentCount,                                                'color' => 'rose',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>'],
                ['label' => 'Attendance Rate',  'value' => $attendanceRate !== null ? $attendanceRate . '%' : 'N/A',   'color' => 'amber',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>'],
            ];
        @endphp
        @foreach($attendanceCards as $card)
        @php $c = $colorMap[$card['color']]; @endphp
        <div class="card p-5">
            <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} flex items-center justify-center mb-4">
                <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    {!! $card['svg'] !!}
                </svg>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
            <p class="text-sm text-gray-500 mt-0.5">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

</x-app-layout>
