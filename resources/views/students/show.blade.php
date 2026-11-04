
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Student Details
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm rounded-lg">

                <h3 class="text-xl font-bold mb-4">
                    {{ $student->first_name }} {{ $student->last_name }}
                </h3>

                @foreach([
                    'Student Code' => 'student_code',
                    'First Name' => 'first_name',
                    'Last Name' => 'last_name',
                    'Email' => 'email',
                    'Phone' => 'phone',
                    'Date of Birth' => 'date_of_birth',
                    'Gender' => 'gender',
                    'Address' => 'address',
                    'Grade' => 'grade',
                    'Class Section' => 'class_section',
                    'Status' => 'status',
                ] as $label => $field)
                    <div class="py-2 border-b">
                        <strong>{{ $label }}:</strong>
                        {{ $student->$field ?? 'N/A' }}
                    </div>
                @endforeach

                <div class="mt-5 flex gap-3">
                    <a href="{{ route('students.edit', $student) }}"
                       class="px-4 py-2 bg-yellow-500 text-white rounded">
                        Edit
                    </a>

                    <a href="{{ route('students.index') }}"
                       class="px-4 py-2 bg-gray-200 rounded">
                        Back
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
