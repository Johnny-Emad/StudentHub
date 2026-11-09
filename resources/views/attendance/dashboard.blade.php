<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Attendance Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                <form method="GET" action="{{ route('attendance.dashboard') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">

                    <div>
                        <label class="block mb-2">From</label>
                        <input type="date" name="from" value="{{ request('from') }}"
                            class="w-full border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="block mb-2">To</label>
                        <input type="date" name="to" value="{{ request('to') }}"
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
                                <option value="{{ $section }}"
                                    @selected(request('class_section') == $section)>
                                    {{ $section }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-4 py-2 bg-gray-800 text-white rounded-md">
                            Apply Filters
                        </button>

                        <a href="{{ route('attendance.dashboard') }}"
                            class="px-4 py-2 bg-gray-200 rounded-md">
                            Reset
                        </a>
                    </div>
                </form>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <div class="p-5 bg-gray-100 rounded-lg">
                        <p class="text-gray-500 text-sm">Total Records</p>
                        <h3 class="text-3xl font-bold mt-2">{{ $total }}</h3>
                    </div>

                    <div class="p-5 bg-green-50 rounded-lg">
                        <p class="text-green-700 text-sm">Present</p>
                        <h3 class="text-3xl font-bold text-green-700 mt-2">
                            {{ $present }}
                        </h3>
                    </div>

                    <div class="p-5 bg-red-50 rounded-lg">
                        <p class="text-red-700 text-sm">Absent</p>
                        <h3 class="text-3xl font-bold text-red-700 mt-2">
                            {{ $absent }}
                        </h3>
                    </div>

                    <div class="p-5 bg-blue-50 rounded-lg">
                        <p class="text-blue-700 text-sm">Attendance Rate</p>
                        <h3 class="text-3xl font-bold text-blue-700 mt-2">
                            {{ $attendanceRate }}%
                        </h3>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>