@php
    $selectedSubject = old('subject_id', $exam?->subject_id);
    $selectedGrade = old('grade', $exam?->grade);

    $grades = $subjects
        ->flatMap(fn ($subject) => $subject->gradeAssignments->pluck('grade'))
        ->unique()
        ->sort()
        ->values();
@endphp

<div class="mb-3">
    <label for="title" class="form-label">Exam Title</label>

    <input
        type="text"
        name="title"
        id="title"
        class="form-control @error('title') is-invalid @enderror"
        value="{{ old('title', $exam?->title) }}"
        required
    >

    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="subject_id" class="form-label">Subject</label>

    <select
        name="subject_id"
        id="subject_id"
        class="form-select @error('subject_id') is-invalid @enderror"
        required
    >
        <option value="">Select Subject</option>

        @foreach($subjects as $subject)
            <option
                value="{{ $subject->id }}"
                data-grades="{{ $subject->gradeAssignments->pluck('grade')->values()->toJson() }}"
                @selected((string) $selectedSubject === (string) $subject->id)
            >
                {{ $subject->name }}
            </option>
        @endforeach
    </select>

    @error('subject_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="grade" class="form-label">Grade</label>

    <select
        name="grade"
        id="grade"
        class="form-select @error('grade') is-invalid @enderror"
        required
    >
        <option value="">Select Grade</option>

        @foreach($grades as $grade)
            <option
                value="{{ $grade }}"
                @selected((string) $selectedGrade === (string) $grade)
            >
                {{ $grade }}
            </option>
        @endforeach
    </select>

    @error('grade')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="class_section" class="form-label">Class Section</label>

    <input
        type="text"
        name="class_section"
        id="class_section"
        class="form-control @error('class_section') is-invalid @enderror"
        value="{{ old('class_section', $exam?->class_section) }}"
        maxlength="10"
        required
    >

    @error('class_section')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="exam_date" class="form-label">Exam Date</label>

    <input
        type="date"
        name="exam_date"
        id="exam_date"
        class="form-control @error('exam_date') is-invalid @enderror"
        value="{{ old('exam_date', $exam?->exam_date) }}"
        required
    >

    @error('exam_date')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="total_marks" class="form-label">Total Marks</label>

    <input
        type="number"
        name="total_marks"
        id="total_marks"
        class="form-control @error('total_marks') is-invalid @enderror"
        value="{{ old('total_marks', $exam?->total_marks ?? 100) }}"
        min="0.01"
        max="999999.99"
        step="0.01"
        required
    >

    @error('total_marks')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const subjectSelect = document.getElementById('subject_id');
        const gradeSelect = document.getElementById('grade');

        function updateGrades() {
            const selectedOption = subjectSelect.options[subjectSelect.selectedIndex];
            const allowedGrades = selectedOption.dataset.grades
                ? JSON.parse(selectedOption.dataset.grades)
                : [];

            const currentGrade = gradeSelect.value;

            gradeSelect.innerHTML = '<option value="">Select Grade</option>';

            allowedGrades.forEach(function (grade) {
                const option = document.createElement('option');

                option.value = grade;
                option.textContent = grade;

                if (grade === currentGrade) {
                    option.selected = true;
                }

                gradeSelect.appendChild(option);
            });
        }

        subjectSelect.addEventListener('change', updateGrades);

        updateGrades();
    });
</script>