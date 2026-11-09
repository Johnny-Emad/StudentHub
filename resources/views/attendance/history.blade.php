<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Attendance History
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                <form method="GET" action="{{ route('attendance.history') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

                    <div>
                        <label class="block mb-2">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}"
                            class="w-full border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="block mb-2">Grade</label>
                        <select name="grade" class="w-full border-gray-300 rounded-md">
                            <option value="">All Grades</option>
                            @foreach ($grades as $grade)
                                <option value="{{ $grade }}" @selected(request('grade') == $grade)>
                                    {{ $grade }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2">Section</label>
                        <select name="class_section" class="w-full border-gray-300 rounded-md">
                            <option value="">All Sections</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section }}" @selected(request('class_section') == $section)>
                                    {{ $section }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2">Student</label>
                        <select name="student" class="w-full border-gray-300 rounded-md">
                            <option value="">All Students</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}" @selected(request('student') == $student->id)>
                                    {{ $student->first_name }} {{ $student->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button class="px-4 py-2 bg-gray-800 text-white rounded-md">
                            Filter
                        </button>

                        <a href="{{ route('attendance.history') }}"
                            class="px-4 py-2 bg-gray-200 rounded-md">
                            Reset
                        </a>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="bg-gray-100 text-gray-700 uppercase">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Student</th>
                                <th class="px-4 py-3">Grade</th>
                                <th class="px-4 py-3">Section</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Recorded By</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($attendances as $attendance)
                                <tr class="border-b">
                                    <td class="px-4 py-3">{{ $attendance->date }}</td>
                                    <td class="px-4 py-3">
                                        {{ $attendance->student->first_name }}
                                        {{ $attendance->student->last_name }}
                                    </td>
                                    <td class="px-4 py-3">{{ $attendance->student->grade }}</td>
                                    <td class="px-4 py-3">{{ $attendance->student->class_section }}</td>
                                    <td class="px-4 py-3">
                                        <span class="{{ $attendance->status === 'present'
                                            ? 'text-green-700' : 'text-red-700' }} font-semibold">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $attendance->marker?->name ?? 'Unknown' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-6">
                                        No attendance records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $attendances->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>