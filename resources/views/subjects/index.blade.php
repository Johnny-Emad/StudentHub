<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Subjects Management
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

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold">All Subjects</h3>

                    <a href="{{ route('admin.subjects.create') }}"
                       class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                        Add Subject
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    #
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Subject Name
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Description
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Assigned Grades
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($subjects as $subject)
                                <tr>
                                    <td class="px-4 py-3">
                                        {{ $subjects->firstItem() + $loop->index }}
                                    </td>

                                    <td class="px-4 py-3 font-medium">
                                        {{ $subject->name }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $subject->description ?: '-' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $subject->grade_assignments_count }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">

                                            <a href="{{ route('admin.subjects.edit', $subject) }}"
                                               class="text-blue-600 hover:text-blue-800">
                                                Edit
                                            </a>

                                            <form
                                                action="{{ route('admin.subjects.destroy', $subject) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this subject?')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-800">
                                                    Delete
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                        No subjects found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <div class="mt-4">
                    {{ $subjects->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>