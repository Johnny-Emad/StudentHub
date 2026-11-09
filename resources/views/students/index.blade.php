
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Students
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Add Student --}}
            <div class="mb-4">
                <a href="{{ route('students.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded">
                    Add Student
                </a>
            </div>

            {{-- Search & Filters --}}
            <div class="bg-white shadow-sm rounded-lg p-4 mb-4">

                <form action="{{ route('students.index') }}" method="GET">

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">

                        {{-- Search --}}
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search by name, code, email or phone"
                            class="border-gray-300 rounded-md"
                        >

                        {{-- Grade --}}
                        <select name="grade" class="border-gray-300 rounded-md">
                            <option value="">All Grades</option>

                            @foreach($grades as $grade)
                                <option value="{{ $grade }}"
                                    {{ request('grade') === $grade ? 'selected' : '' }}>
                                    {{ $grade }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Class Section --}}
                        <select name="class_section" class="border-gray-300 rounded-md">
                            <option value="">All Sections</option>

                            @foreach($sections as $section)
                                <option value="{{ $section }}"
                                    {{ request('class_section') === $section ? 'selected' : '' }}>
                                    {{ $section }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Status --}}
                        <select name="status" class="border-gray-300 rounded-md">
                            <option value="">All Statuses</option>

                            <option value="active"
                                {{ request('status') === 'active' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="inactive"
                                {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>

                        {{-- Buttons --}}
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded">
                                Search
                            </button>

                            <a href="{{ route('students.index') }}"
                               class="px-4 py-2 bg-gray-200 text-gray-700 rounded">
                                Reset
                            </a>
                        </div>

                    </div>
                </form>

            </div>

            {{-- Students Table --}}
            <div class="bg-white shadow-sm rounded-lg overflow-x-auto">

                <table class="w-full text-left">

                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3">Code</th>
                            <th class="p-3">Name</th>
                            <th class="p-3">Grade</th>
                            <th class="p-3">Class</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($students as $student)
                            <tr class="border-t">

                                <td class="p-3">
                                    {{ $student->student_code }}
                                </td>

                                <td class="p-3">
                                    {{ $student->first_name }}
                                    {{ $student->last_name }}
                                </td>

                                <td class="p-3">
                                    {{ $student->grade }}
                                </td>

                                <td class="p-3">
                                    {{ $student->class_section }}
                                </td>

                                <td class="p-3">
                                    {{ ucfirst($student->status) }}
                                </td>

                                <td class="p-3">
                                    <div class="flex flex-wrap gap-2">

                                        <a href="{{ route('students.show', $student) }}"
                                           class="text-blue-600">
                                            View
                                        </a>

                                        <a href="{{ route('students.edit', $student) }}"
                                           class="text-yellow-600">
                                            Edit
                                        </a>

                                        <form action="{{ route('students.destroy', $student) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete this student?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="text-red-600">
                                                Delete
                                            </button>

                                        </form>

                                    </div>
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center">
                                    No students found.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $students->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
