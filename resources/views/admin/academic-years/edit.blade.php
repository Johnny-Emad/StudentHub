<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Academic Year
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg p-6">

                <form action="{{ route('admin.academic-years.update', $academicYear) }}"
                      method="POST">

                    @csrf
                    @method('PUT')

                    <div class="space-y-6">

                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700 mb-2">
                                Academic Year Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name', $academicYear->name) }}"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm"
                            >

                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="start_date" class="block font-medium text-sm text-gray-700 mb-2">
                                Start Date
                            </label>

                            <input
                                type="date"
                                name="start_date"
                                id="start_date"
                                value="{{ old('start_date', $academicYear->start_date->format('Y-m-d')) }}"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm"
                            >

                            @error('start_date')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block font-medium text-sm text-gray-700 mb-2">
                                End Date
                            </label>

                            <input
                                type="date"
                                name="end_date"
                                id="end_date"
                                value="{{ old('end_date', $academicYear->end_date->format('Y-m-d')) }}"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm"
                            >

                            @error('end_date')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                name="is_active"
                                id="is_active"
                                value="1"
                                {{ old('is_active', $academicYear->is_active) ? 'checked' : '' }}
                                class="rounded border-gray-300"
                            >

                            <label for="is_active" class="text-sm text-gray-700">
                                Active Academic Year
                            </label>
                        </div>

                    </div>

                    <div class="flex justify-end gap-3 mt-8">

                        <a href="{{ route('admin.academic-years.index') }}"
                           class="px-4 py-2 border border-gray-300 rounded-md">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Update Academic Year
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>