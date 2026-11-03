<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Teacher Approval
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">

                <h3 class="text-lg font-semibold mb-4">
                    Pending Teacher Requests
                </h3>

                @forelse ($teachers as $teacher)
                    <div class="flex justify-between items-center border-b py-4">

                        <div>
                            <p class="font-semibold">
                                {{ $teacher->name }}
                            </p>

                            <p class="text-sm text-gray-600">
                                {{ $teacher->email }}
                            </p>
                        </div>

                        <div class="flex gap-2">

                            <form method="POST"
                                action="{{ route('admin.teachers.update', [$teacher->id, 'approved']) }}">
                                @csrf
                                @method('PATCH')

                                <button class="bg-green-600 text-white px-4 py-2 rounded">
                                    Approve
                                </button>
                            </form>

                            <form method="POST"
                                action="{{ route('admin.teachers.update', [$teacher->id, 'rejected']) }}">
                                @csrf
                                @method('PATCH')

                                <button class="bg-red-600 text-white px-4 py-2 rounded">
                                    Reject
                                </button>
                            </form>

                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">
                        No pending teacher requests.
                    </p>
                @endforelse

            </div>
        </div>
    </div>
</x-app-layout>