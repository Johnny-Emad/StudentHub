<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <a href="{{ route('admin.subjects.index') }}" class="text-gray-400 hover:text-gray-600 transition">Subjects</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">Assign Grades</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-5">
            <h1 class="text-xl font-bold text-gray-900">Assign Grades</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Select the grades that study <span class="font-semibold text-gray-700">{{ $subject->name }}</span>.
            </p>
        </div>

        <div class="card p-6">
            @if ($errors->any())
                <div class="mb-5 px-4 py-3 rounded-md bg-red-50 border border-red-200 text-sm font-medium text-red-800">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if ($grades->isEmpty())
                <div class="py-8 text-center">
                    <p class="text-sm text-gray-500">No grades are currently available. Add students with grade values first.</p>
                </div>
            @else
                <form action="{{ route('admin.subjects.grades.update', $subject) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-6">
                        @foreach ($grades as $grade)
                            <label class="flex items-center gap-2.5 px-3 py-2.5 border border-gray-200 rounded-md cursor-pointer hover:border-indigo-300 hover:bg-indigo-50/40 transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/60">
                                <input
                                    type="checkbox"
                                    name="grades[]"
                                    value="{{ $grade }}"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                    {{ in_array($grade, old('grades', $assignedGrades->all())) ? 'checked' : '' }}
                                >
                                <span class="text-sm font-medium text-gray-900">{{ $grade }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="flex gap-3 pt-2 border-t border-gray-100">
                        <button type="submit" class="btn-primary">Save Assignments</button>
                        <a href="{{ route('admin.subjects.index') }}" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
