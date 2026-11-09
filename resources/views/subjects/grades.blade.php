<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Assign Grades
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                <h3 class="text-lg font-semibold mb-2">
                    {{ $subject->name }}
                </h3>

                <p class="text-sm text-gray-600 mb-6">
                    Select the grades that study this subject.
                </p>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @if ($grades->isEmpty())
                    <p class="text-gray-600 mb-6">
                        No grades are currently available. Add students with grade values first.
                    </p>
                @else
                    <form action="{{ route('admin.subjects.grades.update', $subject) }}"
                          method="POST">

                        @csrf
                        @method('PUT')

                        <div class="space-y-3 mb-6">
                            @foreach ($grades as $grade)
                                <label class="flex items-center gap-3 p-3 border rounded">
                                    <input
                                        type="checkbox"
                                        name="grades[]"
                                        value="{{ $grade }}"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm"
                                        {{ in_array($grade, old('grades', $assignedGrades->all())) ? 'checked' : '' }}
                                    >

                                    <span class="text-gray-800">
                                        {{ $grade }}
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                                Save Assignments
                            </button>

                            <a href="{{ route('admin.subjects.index') }}"
                               class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                                Cancel
                            </a>
                        </div>

                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>