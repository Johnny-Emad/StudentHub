<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Attendance Management
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                @if (session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="GET" action="{{ route('attendance.index') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

                    <div>
                        <label for="date" class="block text-sm font-medium mb-2">Date</label>
                        <input type="date" name="date" id="date"
                            value="{{ request('date', now()->toDateString()) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label for="grade" class="block text-sm font-medium mb-2">Grade</label>
                        <select name="grade" id="grade"
                            class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">All Grades</option>
                            @foreach ($grades as $grade)
                                <option value="{{ $grade }}" @selected(request('grade') == $grade)>
                                    {{ $grade }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="class_section" class="block text-sm font-medium mb-2">
                            Class Section
                        </label>
                        <select name="class_section" id="class_section"
                            class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">All Sections</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section }}"
                                    @selected(request('class_section') == $section)>
                                    {{ $section }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="px-4 py-2 bg-gray-800 text-white rounded-md">
                            Filter
                        </button>

                        <a href="{{ route('attendance.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
                            Reset
                        </a>
                    </div>
                </form>

                <form method="POST" action="{{ route('attendance.store') }}">
                    @csrf

                    <input type="hidden" name="date"
                        value="{{ request('date', now()->toDateString()) }}">

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3">Student Code</th>
                                    <th class="px-6 py-3">Student Name</th>
                                    <th class="px-6 py-3">Grade</th>
                                    <th class="px-6 py-3">Section</th>
                                    <th class="px-6 py-3">Attendance</th>
                                    <th class="px-6 py-3">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($students as $student)
                                    @php
                                        $attendance = $student->attendances->first();
                                    @endphp

                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-6 py-4">{{ $student->student_code }}</td>

                                        <td class="px-6 py-4">
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </td>

                                        <td class="px-6 py-4">{{ $student->grade }}</td>

                                        <td class="px-6 py-4">{{ $student->class_section }}</td>

                                        <td class="px-6 py-4">
                                            @if ($attendance)
                                                <span class="{{ $attendance->status === 'present'
                                                    ? 'text-green-700' : 'text-red-700' }} font-semibold">
                                                    {{ ucfirst($attendance->status) }}
                                                </span>
                                            @else
                                                <select name="attendance[{{ $student->id }}]"
                                                    class="border-gray-300 rounded-md shadow-sm">
                                                    <option value="present">Present</option>
                                                    <option value="absent">Absent</option>
                                                </select>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4">
                                            @if ($attendance)
                                                <form method="POST"
                                                    action="{{ route('attendance.update', $attendance) }}">
                                                    @csrf
                                                    @method('PATCH')

                                                    <select name="status"
                                                        class="border-gray-300 rounded-md shadow-sm mb-2">
                                                        <option value="present"
                                                            @selected($attendance->status === 'present')>
                                                            Present
                                                        </option>
                                                        <option value="absent"
                                                            @selected($attendance->status === 'absent')>
                                                            Absent
                                                        </option>
                                                    </select>

                                                    <button type="submit"
                                                        class="px-3 py-2 bg-blue-600 text-white rounded-md">
                                                        Update
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400">Not recorded</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-6 text-center">
                                            No students found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($students->contains(fn ($student) => !$student->attendances->isNotEmpty()))
                        <div class="mt-6 flex justify-between items-center">
                            <p class="text-sm text-gray-500">
                                Total students: {{ $students->count() }}
                            </p>

                            <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                                Save Attendance
                            </button>
                        </div>
                    @endif
                </form>

            </div>
        </div>
    </div>
</x-app-layout>