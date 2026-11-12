
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Subject Statistics
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

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
            </div>
        </div>
    </div>
</x-app-layout>
