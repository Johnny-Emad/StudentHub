
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            School Statistics
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Students --}}
            <h3 class="text-lg font-bold mb-4">Students Overview</h3>

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
            <h3 class="text-lg font-bold mb-4">Academic Overview</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-500">Total Subjects</p>
                    <h3 class="text-3xl font-bold">
                        {{ $subjectsCount }}
                    </h3>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-gray-500">Total Exams</p>
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
            <h3 class="text-lg font-bold mb-4">Attendance Overview</h3>

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
