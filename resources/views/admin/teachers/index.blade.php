<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">Teacher Approvals</span>
        </div>
    </x-slot>

    {{-- Flash --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="mb-4 flex items-center gap-2.5 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-sm font-medium text-emerald-800">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-900">Teacher Approvals</h1>
        <p class="text-sm text-gray-500 mt-0.5">Review and manage teacher registration requests.</p>
    </div>

    {{-- Status tabs --}}
    <div class="flex gap-1 p-1 bg-gray-100 rounded-xl w-fit mb-5">
        @php
            $tabs = [
                ['label' => 'Pending',  'status' => 'pending',  'color' => 'amber'],
                ['label' => 'Approved', 'status' => 'approved', 'color' => 'emerald'],
                ['label' => 'Rejected', 'status' => 'rejected', 'color' => 'rose'],
            ];
            $currentStatus = request('status', 'pending');
        @endphp
        @foreach($tabs as $tab)
            <a href="{{ route('admin.teachers.index', ['status' => $tab['status']]) }}"
               class="{{ $currentStatus === $tab['status']
                    ? 'bg-white text-gray-900 shadow-sm'
                    : 'text-gray-500 hover:text-gray-700' }} px-4 py-1.5 rounded-lg text-sm font-medium transition-all">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>

    {{-- Table card --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="table-header">Teacher</th>
                        <th class="table-header">Email</th>
                        <th class="table-header">Status</th>
                        <th class="table-header text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($teachers as $teacher)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="table-cell">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-400 to-indigo-500 flex items-center justify-center shrink-0">
                                        <span class="text-xs font-bold text-white">
                                            {{ strtoupper(substr($teacher->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $teacher->name }}</span>
                                </div>
                            </td>
                            <td class="table-cell text-gray-500">{{ $teacher->email }}</td>
                            <td class="table-cell">
                                @php $s = $teacher->status ?? 'pending'; @endphp
                                @if($s === 'approved')
                                    <span class="badge-green">Approved</span>
                                @elseif($s === 'rejected')
                                    <span class="badge-red">Rejected</span>
                                @else
                                    <span class="badge-yellow">Pending</span>
                                @endif
                            </td>
                            <td class="table-cell">
                                <div class="flex items-center justify-end gap-2">
                                    @if($teacher->status != 'approved')
                                        <form method="POST" action="{{ route('admin.teachers.update', [$teacher->id, 'approved']) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-success btn-sm flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                                </svg>
                                                Approve
                                            </button>
                                        </form>
                                    @endif

                                    @if($teacher->status != 'rejected')
                                        <form method="POST" action="{{ route('admin.teachers.update', [$teacher->id, 'rejected']) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-danger btn-sm flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Reject
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center">
                                <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                                </svg>
                                <p class="text-sm font-medium text-gray-500">No {{ $currentStatus }} teachers</p>
                                <p class="text-xs text-gray-400 mt-1">Check back when new requests come in.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
