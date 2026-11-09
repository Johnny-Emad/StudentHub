@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Create Exam</h2>

    <form action="{{ route('admin.exams.store') }}" method="POST">
        @csrf

        @include('exams.form', ['exam' => null])

        <button type="submit" class="btn btn-primary">Create Exam</button>
        <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </form>
</div>
@endsection