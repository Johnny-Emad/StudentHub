@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Exams Management</h2>

        <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
            Add Exam
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($exams->count())
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Exam Title</th>
                        <th>Subject</th>
                        <th>Grade</th>
                        <th>Class</th>
                        <th>Exam Date</th>
                        <th>Total Marks</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($exams as $exam)
                        <tr>
                            <td>{{ $exam->id }}</td>
                            <td>{{ $exam->title }}</td>
                            <td>{{ $exam->subject->name }}</td>
                            <td>{{ $exam->grade }}</td>
                            <td>{{ $exam->class_section }}</td>
                            <td>{{ $exam->exam_date }}</td>
                            <td>{{ $exam->total_marks }}</td>

                            <td>
                                <a href="{{ route('admin.exams.edit', $exam) }}"
                                   class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <form action="{{ route('admin.exams.destroy', $exam) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this exam?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $exams->links() }}
    @else
        <div class="alert alert-info">
            No exams have been created yet.
        </div>
    @endif
</div>
@endsection