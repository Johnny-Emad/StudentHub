<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">Grade Statistics</span>
        </div>
    </x-slot>

    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-900">Grade Statistics</h1>
        <p class="text-sm text-gray-500 mt-0.5">Performance breakdown per grade level.</p>
    </div>

    @if($grades->isEmpty())
        <div class="card p-10 text-center">
            <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
            </svg>
            <p class="text-sm text-gray-500">No grades available yet.</p>
        </div>
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="table-header">#</th>
                            <th class="table-header">Grade</th>
                            <th class="table-header">Students</th>
                            <th class="table-header">Sections</th>
                            <th class="table-header">Subjects</th>
                            <th class="table-header">Exams</th>
                            <th class="table-header">Grades Recorded</th>
                            <th class="table-header">Overall Average</th>
                            <th class="table-header">Highest Student Avg</th>
                            <th class="table-header">Lowest Student Avg</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($grades as $grade)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="table-cell text-gray-500">{{ $loop->iteration }}</td>
                                <td class="table-cell"><span class="badge-blue">{{ $grade->grade }}</span></td>
                                <td class="table-cell text-gray-700">{{ $grade->students_count }}</td>
                                <td class="table-cell text-gray-700">{{ $grade->sections_count }}</td>
                                <td class="table-cell text-gray-700">{{ $grade->subjects_count }}</td>
                                <td class="table-cell text-gray-700">{{ $grade->exams_count }}</td>
                                <td class="table-cell text-gray-700">{{ $grade->grades_count }}</td>
                                <td class="table-cell font-semibold text-gray-900">
                                    {{ $grade->overall_average !== null ? number_format($grade->overall_average, 2) . '%' : '—' }}
                                </td>
                                <td class="table-cell text-emerald-700">
                                    {{ $grade->highest_average !== null ? number_format($grade->highest_average, 2) . '%' : '—' }}
                                </td>
                                <td class="table-cell text-rose-700">
                                    {{ $grade->lowest_average !== null ? number_format($grade->lowest_average, 2) . '%' : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-app-layout>
