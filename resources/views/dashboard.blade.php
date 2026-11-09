<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-xs font-medium text-gray-400">Overview</span>
        </div>
    </x-slot>

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
            {{ explode(' ', Auth::user()->name)[0] }} 👋
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">Here's what's happening in your school today.</p>
    </div>

    {{-- Stats grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php
            $stats = [
                [
                    'label'   => 'Total Students',
                    'value'   => \App\Models\Student::count(),
                    'change'  => '+12%',
                    'up'      => true,
                    'color'   => 'indigo',
                    'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>'
                ],
                [
                    'label'   => 'Active',
                    'value'   => \App\Models\Student::where('status', 'active')->count(),
                    'change'  => '+5%',
                    'up'      => true,
                    'color'   => 'emerald',
                    'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                ],
                [
                    'label'   => 'Inactive',
                    'value'   => \App\Models\Student::where('status', 'inactive')->count(),
                    'change'  => '-2%',
                    'up'      => false,
                    'color'   => 'rose',
                    'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>'
                ],
                [
                    'label'   => 'Grade Levels',
                    'value'   => \App\Models\Student::distinct('grade')->count('grade'),
                    'change'  => null,
                    'up'      => null,
                    'color'   => 'violet',
                    'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>'
                ],
            ];

            $colorMap = [
                'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
                'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
                'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600'],
                'violet' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600'],
            ];
        @endphp

        @foreach ($stats as $stat)
        @php $c = $colorMap[$stat['color']]; @endphp
        <div class="card p-5">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        {!! $stat['icon'] !!}
                    </svg>
                </div>
                @if($stat['change'])
                    <span class="text-xs font-semibold {{ $stat['up'] ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50' }} px-2 py-0.5 rounded-full">
                        {{ $stat['change'] }}
                    </span>
                @endif
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stat['value'] }}</p>
            <p class="text-sm text-gray-500 mt-0.5">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Quick actions + recent --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Quick Actions --}}
        <div class="card p-5">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('students.create') }}"
                   class="flex items-center gap-3 px-3 py-3 rounded-lg border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50 group transition">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 group-hover:bg-indigo-200 flex items-center justify-center transition">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Add new student</p>
                        <p class="text-xs text-gray-400">Register a student record</p>
                    </div>
                </a>

                <a href="{{ route('students.index') }}"
                   class="flex items-center gap-3 px-3 py-3 rounded-lg border border-gray-100 hover:border-violet-200 hover:bg-violet-50 group transition">
                    <div class="w-8 h-8 rounded-lg bg-violet-100 group-hover:bg-violet-200 flex items-center justify-center transition">
                        <svg class="w-4 h-4 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Browse students</p>
                        <p class="text-xs text-gray-400">Search and filter records</p>
                    </div>
                </a>

                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 px-3 py-3 rounded-lg border border-gray-100 hover:border-gray-200 hover:bg-gray-50 group transition">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-gray-200 flex items-center justify-center transition">
                        <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Edit profile</p>
                        <p class="text-xs text-gray-400">Update your info</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Recent students --}}
        <div class="card p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-900">Recent Students</h2>
                <a href="{{ route('students.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 transition">View all →</a>
            </div>

            @php
                $recentStudents = \App\Models\Student::latest()->take(5)->get();
            @endphp

            @if ($recentStudents->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <svg class="w-10 h-10 text-gray-200 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    <p class="text-sm text-gray-400">No students yet.</p>
                    <a href="{{ route('students.create') }}" class="mt-2 text-sm font-medium text-indigo-600 hover:underline">Add the first one →</a>
                </div>
            @else
                <div class="space-y-0 divide-y divide-gray-50">
                    @foreach ($recentStudents as $student)
                        <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center shrink-0 shadow-sm">
                                <span class="text-xs font-bold text-white">{{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $student->first_name }} {{ $student->last_name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $student->student_code }} • Grade {{ $student->grade }}</p>
                            </div>
                            <span class="{{ $student->status === 'active' ? 'badge-green' : 'badge-gray' }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
