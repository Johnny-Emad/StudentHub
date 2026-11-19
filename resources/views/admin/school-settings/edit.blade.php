<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            School Settings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-semibold mb-6">
                    School Configuration
                </h3>

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form
                    action="{{ route('admin.school-settings.update') }}"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- School Name --}}
                        <div>
                            <label for="school_name"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                School Name
                            </label>

                            <input
                                type="text"
                                id="school_name"
                                name="school_name"
                                value="{{ old('school_name', $settings->school_name) }}"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            >

                            @error('school_name')
                                <p class="text-red-600 text-sm mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Address --}}
                        <div>
                            <label for="address"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                School Address
                            </label>

                            <input
                                type="text"
                                id="address"
                                name="address"
                                value="{{ old('address', $settings->address) }}"
                                class="w-full border-gray-300 rounded-md shadow-sm"
                            >
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label for="phone"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>

                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="{{ old('phone', $settings->phone) }}"
                                class="w-full border-gray-300 rounded-md shadow-sm"
                            >
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                School Email
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $settings->email) }}"
                                class="w-full border-gray-300 rounded-md shadow-sm"
                            >
                        </div>

                        {{-- Academic Year --}}
                        <div>
                            <label for="academic_year"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Current Academic Year
                            </label>

                            <input
                                type="text"
                                id="academic_year"
                                name="academic_year"
                                value="{{ old('academic_year', $settings->academic_year) }}"
                                placeholder="2026/2027"
                                class="w-full border-gray-300 rounded-md shadow-sm"
                            >
                        </div>

                        {{-- Language --}}
                        <div>
                            <label for="default_language"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Default Language
                            </label>

                            <select
                                id="default_language"
                                name="default_language"
                                class="w-full border-gray-300 rounded-md shadow-sm"
                            >
                                <option value="en"
                                    {{ old('default_language', $settings->default_language) === 'en' ? 'selected' : '' }}>
                                    English
                                </option>

                                <option value="ar"
                                    {{ old('default_language', $settings->default_language) === 'ar' ? 'selected' : '' }}>
                                    العربية
                                </option>
                            </select>
                        </div>

                        {{-- Logo --}}
                        <div class="md:col-span-2">
                            <label for="logo"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                School Logo
                            </label>

                            <input
                                type="file"
                                id="logo"
                                name="logo"
                                accept="image/*"
                                class="w-full border-gray-300 rounded-md"
                            >

                            @error('logo')
                                <p class="text-red-600 text-sm mt-1">
                                    {{ $message }}
                                </p>
                            @enderror

                            @if ($settings->logo_path)
                                <div class="mt-4">
                                    <p class="text-sm text-gray-500 mb-2">
                                        Current Logo
                                    </p>

                                    <img
                                        src="{{ asset('storage/' . $settings->logo_path) }}"
                                        alt="School Logo"
                                        class="h-24 w-24 object-contain border rounded p-2"
                                    >
                                </div>
                            @endif
                        </div>

                    </div>

                    {{-- Save Button --}}
                    <div class="mt-8 flex justify-end">
                        <button
                            type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                        >
                            Save Settings
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>