<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <a href="{{ route('admin.exams.index') }}" class="text-gray-400 hover:text-gray-600 transition">Exams</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <a href="{{ route('admin.exams.grades.create', $exam) }}" class="text-gray-400 hover:text-gray-600 transition">Grades</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">Edit Grade</span>
        </div>
    </x-slot>

    <div class="max-w-lg mx-auto">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-900">Edit Student Grade</h1>
            <p class="text-sm text-gray-500 mt-0.5">Update the recorded marks for this student.</p>
        </div>

        {{-- Info card --}}
        <div class="card p-5 mb-5">
            <div class="space-y-2.5">
                <div class="flex justify-between items-center py-1.5 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Exam</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $exam->title }}</span>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Subject</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $exam->subject->name }}</span>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Student</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</span>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Student Code</span>
                    <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-600">{{ $student->student_code }}</span>
                </div>
                <div class="flex justify-between items-center py-1.5">
                    <span class="text-sm text-gray-500">Total Marks</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $exam->total_marks }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.exams.grades.update', [$exam, $examGrade]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card p-6 mb-4">
                <div>
                    <label class="form-label">Marks Obtained <span class="text-red-500">*</span></label>
                    <input type="number"
                           name="marks_obtained"
                           id="marks_obtained"
                           class="form-input w-full"
                           value="{{ old('marks_obtained', $examGrade->marks_obtained) }}"
                           min="0"
                           max="{{ $exam->total_marks }}"
                           step="0.01"
                           required>
                    @error('marks_obtained')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.exams.grades.create', $exam) }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    Update Grade
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
