
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Edit Student
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm rounded-lg">

                <form action="{{ route('students.update', $student) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        @foreach([
                            'student_code' => 'Student Code',
                            'first_name' => 'First Name',
                            'last_name' => 'Last Name',
                            'email' => 'Email',
                            'phone' => 'Phone',
                            'grade' => 'Grade',
                        ] as $field => $label)

                            <div>
                                <label class="block mb-1">{{ $label }}</label>

                                <input
                                    type="text"
                                    name="{{ $field }}"
                                    value="{{ old($field, $student->$field) }}"
                                    maxlength="{{ $field === 'phone' ? 20 : ($field === 'first_name' || $field === 'last_name' ? 100 : 255) }}"
                                    class="w-full border-gray-300 rounded-md"
                                >

                                @error($field)
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                        @endforeach

                        <div>
                            <label class="block mb-1">Class Section</label>

                            <input
                                type="text"
                                name="class_section"
                                value="{{ old('class_section', $student->class_section) }}"
                                maxlength="1"
                                required
                                class="w-full border-gray-300 rounded-md"
                            >

                            @error('class_section')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-1">Date of Birth</label>

                            <input
                                type="date"
                                name="date_of_birth"
                                value="{{ old('date_of_birth', $student->date_of_birth) }}"
                                max="{{ date('Y-m-d') }}"
                                class="w-full border-gray-300 rounded-md"
                            >

                            @error('date_of_birth')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-1">Gender</label>

                            <select name="gender" class="w-full border-gray-300 rounded-md">
                                <option value="">Select Gender</option>
                                <option value="male" @selected(old('gender', $student->gender) === 'male')>Male</option>
                                <option value="female" @selected(old('gender', $student->gender) === 'female')>Female</option>
                            </select>

                            @error('gender')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-1">Status</label>

                            <select name="status" class="w-full border-gray-300 rounded-md">
                                <option value="active" @selected(old('status', $student->status) === 'active')>Active</option>
                                <option value="inactive" @selected(old('status', $student->status) === 'inactive')>Inactive</option>
                            </select>

                            @error('status')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-4">
                        <label class="block mb-1">Address</label>

                        <textarea
                            name="address"
                            maxlength="1000"
                            class="w-full border-gray-300 rounded-md"
                        >{{ old('address', $student->address) }}</textarea>

                        @error('address')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded">
                            Update Student
                        </button>

                        <a href="{{ route('students.index') }}"
                           class="px-4 py-2 bg-gray-200 rounded">
                            Cancel
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
