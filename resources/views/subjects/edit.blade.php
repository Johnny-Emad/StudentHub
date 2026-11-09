<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Subject
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('admin.subjects.update', $subject) }}"
                      method="POST">

                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="block font-medium text-sm text-gray-700">
                            Subject Name
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $subject->name) }}"
                            required
                            maxlength="255"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block font-medium text-sm text-gray-700">
                            Description
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >{{ old('description', $subject->description) }}</textarea>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                            Save Changes
                        </button>

                        <a href="{{ route('admin.subjects.index') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                            Cancel
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>