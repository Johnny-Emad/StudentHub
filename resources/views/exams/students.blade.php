@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Exam Target Students</h2>
            <p class="text-muted mb-0">
                {{ $exam->title }} — {{ $exam->subject->name }}
            </p>
        </div>

        <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
            Back to Exams
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Grade:</strong>
                    {{ $exam->grade }}
                </div>

                <div class="col-md-3">
                    <strong>Section:</strong>
                    {{ $exam->class_section }}
                </div>

                <div class="col-md-3">
                    <strong>Exam Date:</strong>
                    {{ $exam->exam_date }}
                </div>

                <div class="col-md-3">
                    <strong>Total Marks:</strong>
                    {{ $exam->total_marks }}
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <h5>
            Target Students:
            <span class="badge bg-primary">{{ $targetCount }}</span>
        </h5>
    </div>

    @if($students->count())
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Code</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Grade</th>
                        <th>Section</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td>{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                            <td>{{ $student->student_code }}</td>
                            <td>{{ $student->first_name }}</td>
                            <td>{{ $student->last_name }}</td>
                            <td>{{ $student->email ?? '—' }}</td>
                            <td>{{ $student->grade }}</td>
                            <td>{{ $student->class_section }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $students->links() }}
    @else
        <div class="alert alert-info">
            No students found for this exam's grade and section.
        </div>
    @endif

</div>
@endsection