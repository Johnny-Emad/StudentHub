<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <a href="{{ route('admin.subjects.index') }}" class="text-gray-400 hover:text-gray-600 transition">Subjects</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">Edit Subject</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-5">
            <h1 class="text-xl font-bold text-gray-900">Edit Subject</h1>
            <p class="text-sm text-gray-500 mt-0.5">Update the details for {{ $subject->name }}.</p>
        </div>

        <div class="card p-6">
            @if ($errors->any())
                <div class="mb-5 px-4 py-3 rounded-md bg-red-50 border border-red-200 text-sm font-medium text-red-800">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.subjects.update', $subject) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="form-label">Subject Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $subject->name) }}"
                        required
                        maxlength="255"
                        class="form-input"
                    >
                </div>

                <div class="mb-6">
                    <label for="description" class="form-label">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="form-input"
                    >{{ old('description', $subject->description) }}</textarea>
                </div>

                <div class="flex gap-3 pt-2 border-t border-gray-100">
                    <button type="submit" class="btn-primary">Save Changes</button>
                    <a href="{{ route('admin.subjects.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
