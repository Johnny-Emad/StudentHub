<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Academic Years Management
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold">
                        Academic Years
                    </h3>

                    <a href="{{ route('admin.academic-years.create') }}"
                       class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Add Academic Year
                    </a>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if($academicYears->isEmpty())
                    <div class="text-center py-10 text-gray-500">
                        No academic years found.
                    </div>
                @else

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Name
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Start Date
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        End Date
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Status
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">

                                @foreach($academicYears as $academicYear)
                                    <tr>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $academicYear->name }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $academicYear->start_date->format('Y-m-d') }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $academicYear->end_date->format('Y-m-d') }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($academicYear->is_active)
                                                <span class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded-full">
                                                    Active
                                                </span>
                                            @else
                                                <span class="px-3 py-1 text-sm bg-gray-100 text-gray-600 rounded-full">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">

                                                <a href="{{ route('admin.academic-years.edit', $academicYear) }}"
                                                   class="text-indigo-600 hover:text-indigo-900">
                                                    Edit
                                                </a>

                                                @if(!$academicYear->is_active)
                                                    <form action="{{ route('admin.academic-years.activate', $academicYear) }}"
                                                          method="POST">
                                                        @csrf
                                                        @method('PATCH')

                                                        <button type="submit"
                                                                class="text-green-600 hover:text-green-900">
                                                            Activate
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('admin.academic-years.destroy', $academicYear) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Are you sure you want to delete this academic year?')">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                                class="text-red-600 hover:text-red-900">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif

                                            </div>
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>
                    </div>

                @endif

            </div>

        </div>
    </div>
</x-app-layout>