<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <a href="{{ route('students.index') }}" class="text-gray-400 hover:text-gray-600 transition">Students</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">Add Student</span>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-900">Add New Student</h1>
            <p class="text-sm text-gray-500 mt-0.5">Fill in the student's details below.</p>
        </div>

        <form action="{{ route('students.store') }}" method="POST">
            @csrf

            {{-- Personal Information --}}
            <div class="card p-6 mb-4">
                <h2 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-bold">1</span>
                    Personal Information
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="form-label">Student Code <span class="text-red-500">*</span></label>
                        <input type="text" name="student_code" value="{{ old('student_code') }}"
                               class="form-input w-full" placeholder="e.g. STU-2024-001">
                        @error('student_code')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}"
                               class="form-input w-full" placeholder="Jane">
                        @error('first_name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}"
                               class="form-input w-full" placeholder="Smith">
                        @error('last_name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="form-input w-full" placeholder="student@example.com">
                        @error('email')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="form-input w-full" placeholder="01XXXXXXXXX"
                               maxlength="11" minlength="11" inputmode="numeric" pattern="[0-9]{11}">
                        @error('phone')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                               class="form-input w-full" max="{{ date('Y-m-d') }}">
                        @error('date_of_birth')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-input w-full">
                            <option value="">Select gender</option>
                            <option value="male" @selected(old('gender') === 'male')>Male</option>
                            <option value="female" @selected(old('gender') === 'female')>Female</option>
                        </select>
                        @error('gender')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Address</label>
                        <textarea name="address" rows="1" maxlength="1000"
                                  class="form-input w-full resize-none"
                                  placeholder="Street, city...">{{ old('address') }}</textarea>
                        @error('address')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Academic Information --}}
            <div class="card p-6 mb-6">
                <h2 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-bold">2</span>
                    Academic Information
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    <div>
                        <label class="form-label">Grade <span class="text-red-500">*</span></label>
                        <input type="text" name="grade" value="{{ old('grade') }}"
                               class="form-input w-full" placeholder="e.g. 10">
                        @error('grade')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Class Section <span class="text-red-500">*</span></label>
                        <input type="text" name="class_section" value="{{ old('class_section') }}"
                               class="form-input w-full" placeholder="e.g. A" maxlength="1">
                        @error('class_section')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input w-full">
                            <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                            <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                        </select>
                        @error('status')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Form actions --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('students.index') }}" class="btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn-primary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Save Student
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
