
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}"
                class="text-gray-400 hover:text-gray-600 transition">
                Dashboard
            </a>

            <svg class="w-4 h-4 text-gray-300" fill="none"
                viewBox="0 0 24 24" stroke-width="2.5"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>

            <span class="text-gray-700 font-medium">
                Subject Statistics
            </span>
        </div>
    </x-slot>

    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-900">
            Subject Statistics
        </h1>

        <p class="text-sm text-gray-500 mt-0.5">
            Exam coverage and grade performance per subject.
        </p>
    </div>

    <div class="card overflow-hidden">

        @if($subjects->isEmpty())

            <div class="py-16 text-center">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3"
                    fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>

                <p class="text-sm font-medium text-gray-500">
                    No subjects available yet
                </p>
            </div>

        @else

            <div class="overflow-x-auto">
                <table class="w-full">

                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="table-header w-12">#</th>
                            <th class="table-header">Subject</th>
                            <th class="table-header text-center">Exams</th>
                            <th class="table-header text-center">Targeted Students</th>
                            <th class="table-header text-center">Grades Recorded</th>
                            <th class="table-header text-center">Overall Avg</th>
                            <th class="table-header text-center">Highest Avg</th>
                            <th class="table-header text-center">Lowest Avg</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @foreach($subjects as $subject)

                            <tr class="hover:bg-gray-50/60 transition">

                                <td class="table-cell text-gray-400">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="table-cell font-semibold text-gray-900">
                                    {{ $subject->name }}
                                </td>

                                <td class="table-cell text-center text-gray-600">
                                    {{ $subject->exams_count }}
                                </td>

                                <td class="table-cell text-center text-gray-600">
                                    {{ $subject->targeted_students }}
                                </td>

                                <td class="table-cell text-center text-gray-600">
                                    {{ $subject->graded_count }}
                                </td>

                                <td class="table-cell text-center font-semibold text-gray-900">
                                    @if($subject->overall_average !== null)
                                        {{ number_format($subject->overall_average, 2) }}%
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                <td class="table-cell text-center text-emerald-700 font-medium">
                                    @if($subject->highest_average !== null)
                                        {{ number_format($subject->highest_average, 2) }}%
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                <td class="table-cell text-center text-red-700 font-medium">
                                    @if($subject->lowest_average !== null)
                                        {{ number_format($subject->lowest_average, 2) }}%
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                            </tr>

                        @endforeach

                    </tbody>
                </table>
            </div>

        @endif

    </div>
</x-app-layout>
