<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <a href="{{ route('students.index') }}" class="text-gray-400 hover:text-gray-600 transition">Students</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">{{ $student->first_name }} {{ $student->last_name }}</span>
        </div>
    </x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('students.edit', $student) }}" class="btn-primary btn-sm flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
            </svg>
            Edit
        </a>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        {{-- Student header card --}}
        <div class="card p-6 mb-4">
            <div class="flex items-start gap-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center shrink-0 shadow-md">
                    <span class="text-xl font-bold text-white">
                        {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                    </span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-xl font-bold text-gray-900">
                            {{ $student->first_name }} {{ $student->last_name }}
                        </h1>
                        @if($student->status === 'active')
                            <span class="badge-green">Active</span>
                        @else
                            <span class="badge-gray">Inactive</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-4 mt-1.5 flex-wrap">
                        <span class="flex items-center gap-1 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5"/>
                            </svg>
                            <span class="font-mono">{{ $student->student_code }}</span>
                        </span>
                        <span class="flex items-center gap-1 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                            </svg>
                            Grade {{ $student->grade }} — Section {{ $student->class_section }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Details grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            {{-- Contact Info --}}
            <div class="card p-5">
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Contact</h2>
                <div class="space-y-3">
                    @php
                        $contact = [
                            ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"', 'label' => 'Email', 'value' => $student->email],
                            ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"', 'label' => 'Phone', 'value' => $student->phone],
                            ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"', 'label' => 'Address', 'value' => $student->address],
                        ];
                    @endphp
                    @foreach($contact as $row)
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                    {!! $row['icon'] !!}
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">{{ $row['label'] }}</p>
                                <p class="text-sm text-gray-800 mt-0.5">{{ $row['value'] ?? '—' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Personal Info --}}
            <div class="card p-5">
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Personal</h2>
                <div class="space-y-3">
                    @php
                        $personal = [
                            ['label' => 'Date of Birth', 'value' => $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') : null],
                            ['label' => 'Gender', 'value' => $student->gender ? ucfirst($student->gender) : null],
                        ];
                    @endphp
                    @foreach($personal as $row)
                        <div>
                            <p class="text-xs text-gray-400 font-medium">{{ $row['label'] }}</p>
                            <p class="text-sm text-gray-800 mt-0.5">{{ $row['value'] ?? '—' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Back link --}}
        <a href="{{ route('students.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-900 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            Back to students
        </a>
    </div>
</x-app-layout>
