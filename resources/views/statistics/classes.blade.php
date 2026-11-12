
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Class Statistics</h2>

    @if($classes->isEmpty())
        <div class="alert alert-info">
            No classes available yet.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Grade</th>
                        <th>Section</th>
                        <th>Students</th>
                        <th>Subjects</th>
                        <th>Exams</th>
                        <th>Grades Recorded</th>
                        <th>Average</th>
                        <th>Highest Average</th>
                        <th>Lowest Average</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($classes as $class)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $class->grade }}</td>
                            <td>{{ $class->class_section }}</td>
                            <td>{{ $class->students_count }}</td>
                            <td>{{ $class->subjects_count }}</td>
                            <td>{{ $class->exams_count }}</td>
                            <td>{{ $class->grades_count }}</td>

                            <td>
                                {{ $class->average !== null
                                    ? number_format($class->average, 2) . '%'
                                    : '—' }}
                            </td>

                            <td>
                                {{ $class->highest_average !== null
                                    ? number_format($class->highest_average, 2) . '%'
                                    : '—' }}
                            </td>

                            <td>
                                {{ $class->lowest_average !== null
                                    ? number_format($class->lowest_average, 2) . '%'
                                    : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
