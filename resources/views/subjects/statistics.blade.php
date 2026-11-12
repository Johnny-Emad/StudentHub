
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Subject Statistics</h2>

    @if($subjects->isEmpty())
        <div class="alert alert-info">
            No subjects available yet.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Subject</th>
                        <th>Exams</th>
                        <th>Targeted Students</th>
                        <th>Grades Recorded</th>
                        <th>Overall Average</th>
                        <th>Highest Average</th>
                        <th>Lowest Average</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($subjects as $subject)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $subject->name }}</td>

                            <td>{{ $subject->exams_count }}</td>

                            <td>{{ $subject->targeted_students }}</td>

                            <td>{{ $subject->graded_count }}</td>

                            <td>
                                @if($subject->overall_average !== null)
                                    {{ number_format($subject->overall_average, 2) }}%
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                @if($subject->highest_average !== null)
                                    {{ number_format($subject->highest_average, 2) }}%
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                @if($subject->lowest_average !== null)
                                    {{ number_format($subject->lowest_average, 2) }}%
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
