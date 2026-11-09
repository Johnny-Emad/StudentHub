<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Attendance History
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                @if (session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('attendance.history') }}"
                      class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div>
                        <label for="from" class="block text-sm font-medium text-gray-700">
                            From
                        </label>
                        <input
                            type="date"
                            id="from"
                            name="from"
                            value="{{ request('from') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label for="to" class="block text-sm font-medium text-gray-700">
                            To
                        </label>
                        <input
                            type="date"
                            id="to"
                            name="to"
                            value="{{ request('to') }}"
                            min="{{ request('from') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label for="grade" class="block text-sm font-medium text-gray-700">
                            Grade
                        </label>
                        <select
                            id="grade"
                            name="grade"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                            <option value="">All Grades</option>
                            @foreach ($grades as $grade)
                                <option value="{{ $grade }}"
                                    {{ request('grade') == $grade ? 'selected' : '' }}>
                                    {{ $grade }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="class_section" class="block text-sm font-medium text-gray-700">
                            Class Section
                        </label>
                        <select
                            id="class_section"
                            name="class_section"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                            <option value="">All Sections</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section }}"
                                    {{ request('class_section') == $section ? 'selected' : '' }}>
                                    {{ $section }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="student" class="block text-sm font-medium text-gray-700">
                            Student
                        </label>
                        <select
                            id="student"
                            name="student"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                            <option value="">All Students</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}"
                                    {{ request('student') == $student->id ? 'selected' : '' }}>
                                    {{ $student->first_name }} {{ $student->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ route('attendance.history') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                        >
                            Reset
                        </a>
                    </div>
                </form>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Date
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Student
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Grade
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Section
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Status
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Recorded By
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($attendances as $attendance)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ $attendance->date }}
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if ($attendance->student)
                                            {{ $attendance->student->first_name }}
                                            {{ $attendance->student->last_name }}
                                        @else
                                            Unknown Student
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ $attendance->student->grade ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ $attendance->student->class_section ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ ucfirst($attendance->status) }}
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ $attendance->marker->name ?? 'Unknown' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                        No attendance records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $attendances->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>