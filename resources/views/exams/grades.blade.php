
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-3">Enter Exam Grades</h2>

    <div class="card mb-4">
        <div class="card-body">
            <h5>{{ $exam->title }}</h5>

            <p class="mb-1">
                <strong>Subject:</strong> {{ $exam->subject->name }}
            </p>

            <p class="mb-1">
                <strong>Grade:</strong> {{ $exam->grade }}
            </p>

            <p class="mb-1">
                <strong>Section:</strong> {{ $exam->class_section }}
            </p>

            <p class="mb-0">
                <strong>Total Marks:</strong> {{ $exam->total_marks }}
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-4 mb-2">
            <div class="card">
                <div class="card-body">
                    <h6>Total Students</h6>
                    <h3>{{ $targetCount }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="card">
                <div class="card-body">
                    <h6>Graded</h6>
                    <h3 class="text-success">{{ $gradedCount }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="card">
                <div class="card-body">
                    <h6>Not Graded</h6>
                    <h3 class="text-danger">{{ $notGradedCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    <form method="GET"
          action="{{ route('admin.exams.grades.create', $exam) }}"
          class="mb-3">

        <div class="row align-items-end">
            <div class="col-md-4">
                <label for="status" class="form-label">
                    Filter by Grade Status
                </label>

                <select name="status" id="status" class="form-select">
                    <option value="all"
                        {{ $statusFilter === 'all' ? 'selected' : '' }}>
                        All Students
                    </option>

                    <option value="graded"
                        {{ $statusFilter === 'graded' ? 'selected' : '' }}>
                        Graded
                    </option>

                    <option value="not_graded"
                        {{ $statusFilter === 'not_graded' ? 'selected' : '' }}>
                        Not Graded
                    </option>
                </select>
            </div>

            <div class="col-md-2 mt-2">
                <button type="submit" class="btn btn-primary">
                    Filter
                </button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Code</th>
                    <th>Student Name</th>
                    <th>Marks Obtained</th>
                    <th>Subject Average</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($students as $student)
                    @php
                        $existingGrade = $student->examGrades->first();
                    @endphp

                    <tr>
                        <td>{{ $students->firstItem() + $loop->index }}</td>

                        <td>{{ $student->student_code }}</td>

                        <td>
                            {{ $student->first_name }}
                            {{ $student->last_name }}
                        </td>

                        <td>
                            <form
                                method="POST"
                                action="{{ route('admin.exams.grades.store', $exam) }}"
                                class="d-flex gap-2">

                                @csrf

                                <input
                                    type="number"
                                    name="grades[{{ $student->id }}]"
                                    class="form-control"
                                    min="0"
                                    max="{{ $exam->total_marks }}"
                                    step="0.01"
                                    value="{{ old(
                                        'grades.' . $student->id,
                                        $existingGrade?->marks_obtained
                                    ) }}"
                                    placeholder="Enter marks"
                                >

                                <button type="submit" class="btn btn-success">
                                    Save
                                </button>
                            </form>
                        </td>

                        <td>
                            @if($student->subject_average !== null)
                                <strong>
                                    {{ number_format($student->subject_average, 2) }}%
                                </strong>
                            @else
                                <span class="text-muted">No grades yet</span>
                            @endif
                        </td>

                        <td>
                            @if($existingGrade)
                                <span class="badge bg-success">
                                    Graded
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    Not Graded
                                </span>
                            @endif
                        </td>

                        <td>
                            @if($existingGrade)
                                <a
                                    href="{{ route(
                                        'admin.exams.grades.edit',
                                        [$exam, $existingGrade]
                                    ) }}"
                                    class="btn btn-sm btn-primary">
                                    Edit
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            No students found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $students->links() }}
    </div>
</div>
@endsection
