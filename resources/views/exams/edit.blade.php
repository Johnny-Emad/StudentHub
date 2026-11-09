@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Exam</h2>

    <form action="{{ route('admin.exams.update', $exam) }}" method="POST">
        @csrf
        @method('PUT')

        @include('exams.form', ['exam' => $exam])

        <button type="submit" class="btn btn-primary">Update Exam</button>
        <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </form>
</div>
@endsection