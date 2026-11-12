<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'StudentHub') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen">

    <div class="min-h-screen flex">
        {{-- Brand panel --}}
        <div class="hidden lg:flex lg:w-[45%] xl:w-1/2 bg-indigo-900 relative flex-col justify-between p-12 xl:p-16">
            {{-- Subtle grid texture --}}
            <div class="absolute inset-0 opacity-[0.07]">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="48" height="48" patternUnits="userSpaceOnUse">
                            <path d="M 48 0 L 0 0 0 48" fill="none" stroke="white" stroke-width="1"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
            </div>

            {{-- Brand --}}
            <div class="relative z-10 flex items-center gap-3">
                <div class="w-10 h-10 rounded-md bg-white/10 border border-white/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-white tracking-tight">StudentHub</span>
            </div>

            {{-- Intro + features --}}
            <div class="relative z-10 max-w-md">
                <h2 class="text-3xl font-bold text-white leading-snug mb-4">
                    One platform for our school's management needs.
                </h2>
                <p class="text-indigo-200/90 text-base leading-relaxed mb-10">
                    StudentHub is the internal management system for our school — helping the administration, teachers, and students stay organized and connected.
                </p>

                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-5 h-5 rounded bg-white/10 border border-white/15 flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white">Student records</p>
                            <p class="text-sm text-indigo-200/80 mt-0.5">Manage enrollment, grades, and sections in one place.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-5 h-5 rounded bg-white/10 border border-white/15 flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white">Attendance &amp; exams</p>
                            <p class="text-sm text-indigo-200/80 mt-0.5">Track daily attendance and manage examinations and grades.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-5 h-5 rounded bg-white/10 border border-white/15 flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white">School-wide insights</p>
                            <p class="text-sm text-indigo-200/80 mt-0.5">Clear statistics and reports for our school's operations.</p>
                        </div>
                    </li>
                </ul>
            </div>

            {{-- Footer --}}
            <div class="relative z-10">
                <p class="text-indigo-300/70 text-xs">© {{ date('Y') }} StudentHub — Internal school management platform.</p>
            </div>
        </div>

        {{-- Form panel --}}
        <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 sm:px-10 lg:px-16">
            {{-- Mobile logo --}}
            <div class="lg:hidden flex items-center gap-2.5 mb-10">
                <div class="w-9 h-9 rounded-md bg-indigo-700 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-gray-900">StudentHub</span>
            </div>

            <div class="w-full max-w-sm">
                {{ $slot }}
            </div>
        </div>
    </div>

</body>
</html>
