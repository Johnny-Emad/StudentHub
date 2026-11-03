<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Teacher Management Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Flash Success Message -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg shadow-sm flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Dashboard Navigation Tabs -->
            <div class="flex space-x-2 border-b border-gray-200 mb-6 pb-2">
                <a href="{{ route('admin.teachers.index', ['status' => 'pending']) }}" 
                   class="px-4 py-2 font-medium text-sm rounded-lg transition {{ request('status', 'pending') == 'pending' ? 'bg-indigo-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    Pending Requests
                </a>
                <a href="{{ route('admin.teachers.index', ['status' => 'approved']) }}" 
                   class="px-4 py-2 font-medium text-sm rounded-lg transition {{ request('status') == 'approved' ? 'bg-indigo-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    Approved Teachers
                </a>
                <a href="{{ route('admin.teachers.index', ['status' => 'rejected']) }}" 
                   class="px-4 py-2 font-medium text-sm rounded-lg transition {{ request('status') == 'rejected' ? 'bg-indigo-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    Rejected Requests
                </a>
            </div>

            <!-- Main Card Container -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800 capitalize">
                            {{ request('status', 'pending') }} Teachers List
                        </h3>
                    </div>

                    <!-- Teachers Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($teachers as $teacher)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $teacher->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $teacher->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $teacher->status == 'approved' ? 'bg-green-100 text-green-800' : ($teacher->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($teacher->status ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                @if($teacher->status != 'approved')
                                                    <form method="POST" action="{{ route('admin.teachers.update', [$teacher->id, 'approved']) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow transition">
                                                            Approve
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($teacher->status != 'rejected')
                                                    <form method="POST" action="{{ route('admin.teachers.update', [$teacher->id, 'rejected']) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow transition">
                                                            Reject
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-500 text-sm">
                                            No teachers found in this section.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>