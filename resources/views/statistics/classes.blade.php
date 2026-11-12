<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">Class Statistics</span>
        </div>
    </x-slot>

    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-900">Class Statistics</h1>
        <p class="text-sm text-gray-500 mt-0.5">Performance breakdown per class and section.</p>
    </div>

    @if($classes->isEmpty())
        <div class="card p-10 text-center">
            <p class="text-sm text-gray-500">No classes available yet.</p>
        </div>
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/60">
                            <th class="table-header">#</th>
                            <th class="table-header">Grade</th>
                            <th class="table-header">Section</th>
                            <th class="table-header">Students</th>
                            <th class="table-header">Subjects</th>
                            <th class="table-header">Exams</th>
                            <th class="table-header">Grades Recorded</th>
                            <th class="table-header">Average</th>
                            <th class="table-header">Highest Average</th>
                            <th class="table-header">Lowest Average</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($classes as $class)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="table-cell text-gray-500">{{ $loop->iteration }}</td>
                                <td class="table-cell"><span class="badge-blue">{{ $class->grade }}</span></td>
                                <td class="table-cell font-medium text-gray-900">{{ $class->class_section }}</td>
                                <td class="table-cell text-gray-700">{{ $class->students_count }}</td>
                                <td class="table-cell text-gray-700">{{ $class->subjects_count }}</td>
                                <td class="table-cell text-gray-700">{{ $class->exams_count }}</td>
                                <td class="table-cell text-gray-700">{{ $class->grades_count }}</td>
                                <td class="table-cell font-semibold text-gray-900">
                                    {{ $class->average !== null ? number_format($class->average, 2) . '%' : '—' }}
                                </td>
                                <td class="table-cell text-emerald-700">
                                    {{ $class->highest_average !== null ? number_format($class->highest_average, 2) . '%' : '—' }}
                                </td>
                                <td class="table-cell text-rose-700">
                                    {{ $class->lowest_average !== null ? number_format($class->lowest_average, 2) . '%' : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-app-layout>
