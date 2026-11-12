
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            School Statistics
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Date Filter --}}
            <div class="bg-white p-6 rounded-lg shadow mb-8">
                <h3 class="text-lg font-bold mb-4">
                    Filter Statistics by Date
                </h3>

                <form method="GET"
                      action="{{ route('admin.statistics.school') }}">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div>
                            <label for="from"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                From
                            </label>

                            <input
                                type="date"
                                name="from"
                                id="from"
                                value="{{ $from }}"
                                class="w-full border-gray-300 rounded-md shadow-sm"
                            >
                        </div>

                        <div>
                            <label for="to"
                                   class="block text-sm font-medium text-gray-700 mb-1">
                                To
                            </label>

                            <input
                                type="date"
                                name="to"
                                id="to"
                                value="{{ $to }}"
                                min="{{ $from }}"
                                class="w-full border-gray-300 rounded-md shadow-sm"
                            >
                        </div>

                        <div class="flex items-end gap-2">
                            <button
                                type="submit"
                                class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700"
                            >
                                Apply Filter
                            </button>

                            <a
                                href="{{ route('admin.statistics.school') }}"
                                class="bg-gray-200 text-gray-800 px-5 py-2 rounded-md hover:bg-gray-300"
                            >
                                Reset
                            </a>
                        </div>

                    </div>
                </form>
            </div>

            {{-- Students Overview --}}
            <h3 class="text-lg font-bold mb-4">
                Students Overview
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-500">Total Students</p>
                    <h3 class="text-3xl font-bold">
                        {{ $studentsCount }}
                    </h3>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-500">Total Grades</p>
                    <h3 class="text-3xl font-bold">
                        {{ $gradesCount }}
                    </h3>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-500">Total Sections</p>
                    <h3 class="text-3xl font-bold">
                        {{ $sectionsCount }}
                    </h3>
                </div>

            </div>

            {{-- Academic Overview --}}
            <h3 class="text-lg font-bold mb-4">
                Academic Overview
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-500">Total Subjects</p>
                    <h3 class="text-3xl font-bold">
                        {{ $subjectsCount }}
                    </h3>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-500">Exams in Period</p>
                    <h3 class="text-3xl font-bold">
                        {{ $examsCount }}
                    </h3>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-500">Recorded Grades</p>
                    <h3 class="text-3xl font-bold">
                        {{ $gradesCountRecorded }}
                    </h3>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-500">Overall Average</p>
                    <h3 class="text-3xl font-bold">
                        {{ $overallAverage !== null ? $overallAverage . '%' : 'N/A' }}
                    </h3>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-500">Highest Student Average</p>
                    <h3 class="text-3xl font-bold">
                        {{ $highestAverage !== null ? $highestAverage . '%' : 'N/A' }}
                    </h3>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-500">Lowest Student Average</p>
                    <h3 class="text-3xl font-bold">
                        {{ $lowestAverage !== null ? $lowestAverage . '%' : 'N/A' }}
                    </h3>
                </div>

            </div>

            {{-- Attendance Overview --}}
            <h3 class="text-lg font-bold mb-4">
                Attendance Overview
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-500">Attendance Records</p>
                    <h3 class="text-3xl font-bold">
                        {{ $attendanceCount }}
                    </h3>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-500">Present Records</p>
                    <h3 class="text-3xl font-bold text-green-600">
                        {{ $presentCount }}
                    </h3>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-500">Absent Records</p>
                    <h3 class="text-3xl font-bold text-red-600">
                        {{ $absentCount }}
                    </h3>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-500">Attendance Rate</p>
                    <h3 class="text-3xl font-bold">
                        {{ $attendanceRate !== null ? $attendanceRate . '%' : 'N/A' }}
                    </h3>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
