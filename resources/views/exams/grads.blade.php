@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Bulk Grades Entry</h2>
            <p class="text-muted mb-0">
                {{ $exam->title }} — {{ $exam->subject->name }}
            </p>
        </div>

        <a href="{{ route('admin.exams.students', $exam) }}"
           class="btn btn-secondary">
            Back to Students
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <strong>Grade:</strong> {{ $exam->grade }}
            <span class="mx-3"></span>
            <strong>Section:</strong> {{ $exam->class_section }}
            <span class="mx-3"></span>
            <strong>Total Marks:</strong> {{ $exam->total_marks }}
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($students->count())
        <form action="{{ route('admin.exams.grades.store', $exam) }}"
              method="POST">

            @csrf

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Code</th>
                            <th>Student Name</th>
                            <th>Marks Obtained</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($students as $student)
                            @php
                                $existingGrade = $student->examGrades->first();
                            @endphp

                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>{{ $student->student_code }}</td>

                                <td>
                                    {{ $student->first_name }}
                                    {{ $student->last_name }}
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        name="grades[{{ $student->id }}]"
                                        class="form-control @error('grades.' . $student->id) is-invalid @enderror"
                                        value="{{ old('grades.' . $student->id, $existingGrade?->marks_obtained) }}"
                                        min="0"
                                        max="{{ $exam->total_marks }}"
                                        step="0.01"
                                        required
                                    >

                                    @error('grades.' . $student->id)
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @error('grades')
                <div class="alert alert-danger">
                    {{ $message }}
                </div>
            @enderror

            <button type="submit" class="btn btn-primary">
                Save All Grades
            </button>

        </form>
    @else
        <div class="alert alert-info">
            No students are assigned to this exam.
        </div>
    @endif

</div>
@endsection