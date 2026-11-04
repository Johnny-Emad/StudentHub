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

            <div class="mb-4">
                <a href="{{ route('students.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded">
                    Add Student
                </a>
            </div>

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
                                <td class="p-3">{{ $student->student_code }}</td>

                                <td class="p-3">
                                    {{ $student->first_name }}
                                    {{ $student->last_name }}
                                </td>

                                <td class="p-3">{{ $student->grade }}</td>
                                <td class="p-3">{{ $student->class_section }}</td>
                                <td class="p-3">{{ ucfirst($student->status) }}</td>

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

            <div class="mt-4">
                {{ $students->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
