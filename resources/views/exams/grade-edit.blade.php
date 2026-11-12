@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">Edit Student Grade</h2>

    <div class="card mb-4">
        <div class="card-body">
            <p>
                <strong>Exam:</strong>
                {{ $exam->title }}
            </p>

            <p>
                <strong>Subject:</strong>
                {{ $exam->subject->name }}
            </p>

            <p>
                <strong>Student:</strong>
                {{ $student->first_name }} {{ $student->last_name }}
            </p>

            <p>
                <strong>Student Code:</strong>
                {{ $student->student_code }}
            </p>

            <p>
                <strong>Total Marks:</strong>
                {{ $exam->total_marks }}
            </p>
        </div>
    </div>

    <form action="{{ route('admin.exams.grades.update', [$exam, $examGrade]) }}"
          method="POST">

        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="marks_obtained" class="form-label">
                Marks Obtained
            </label>

            <input
                type="number"
                name="marks_obtained"
                id="marks_obtained"
                class="form-control @error('marks_obtained') is-invalid @enderror"
                value="{{ old('marks_obtained', $examGrade->marks_obtained) }}"
                min="0"
                max="{{ $exam->total_marks }}"
                step="0.01"
                required
            >

            @error('marks_obtained')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            Update Grade
        </button>

        <a href="{{ route('admin.exams.grades.create', $exam) }}"
           class="btn btn-secondary">
            Cancel
        </a>

    </form>

</div>
@endsection