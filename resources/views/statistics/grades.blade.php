
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Grade Statistics</h2>

    @if($grades->isEmpty())
        <div class="alert alert-info">
            No grades available yet.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Grade</th>
                        <th>Students</th>
                        <th>Sections</th>
                        <th>Subjects</th>
                        <th>Exams</th>
                        <th>Grades Recorded</th>
                        <th>Overall Average</th>
                        <th>Highest Student Average</th>
                        <th>Lowest Student Average</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($grades as $grade)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $grade->grade }}</td>

                            <td>{{ $grade->students_count }}</td>

                            <td>{{ $grade->sections_count }}</td>

                            <td>{{ $grade->subjects_count }}</td>

                            <td>{{ $grade->exams_count }}</td>

                            <td>{{ $grade->grades_count }}</td>

                            <td>
                                @if($grade->overall_average !== null)
                                    {{ number_format($grade->overall_average, 2) }}%
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                @if($grade->highest_average !== null)
                                    {{ number_format($grade->highest_average, 2) }}%
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                @if($grade->lowest_average !== null)
                                    {{ number_format($grade->lowest_average, 2) }}%
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
