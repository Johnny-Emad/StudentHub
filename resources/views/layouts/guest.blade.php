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
        {{-- Left decorative panel --}}
        <div class="hidden lg:flex lg:w-1/2 xl:w-[55%] bg-indigo-600 relative overflow-hidden flex-col justify-between p-12">
            {{-- Pattern background --}}
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse">
                            <path d="M 60 0 L 0 0 0 60" fill="none" stroke="white" stroke-width="1"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
            </div>

            {{-- Gradient blobs --}}
            <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-500 rounded-full blur-3xl opacity-40 -translate-y-1/3 translate-x-1/3"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-violet-600 rounded-full blur-3xl opacity-40 translate-y-1/3 -translate-x-1/4"></div>

            {{-- Brand --}}
            <div class="relative z-10 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-white tracking-tight">StudentHub</span>
            </div>

            {{-- Quote / copy --}}
            <div class="relative z-10">
                <blockquote class="text-3xl font-bold text-white leading-snug mb-6">
                    "Empowering educators<br>to manage smarter."
                </blockquote>
                <p class="text-indigo-200 text-base leading-relaxed max-w-sm">
                    A modern platform for tracking student progress, managing records, and streamlining academic workflows — all in one place.
                </p>

                {{-- Stats row --}}
                <div class="flex gap-8 mt-10">
                    <div>
                        <p class="text-2xl font-bold text-white">5,000+</p>
                        <p class="text-indigo-200 text-sm mt-0.5">Students tracked</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">200+</p>
                        <p class="text-indigo-200 text-sm mt-0.5">Active teachers</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">50+</p>
                        <p class="text-indigo-200 text-sm mt-0.5">Schools served</p>
                    </div>
                </div>
            </div>

            {{-- Bottom credits --}}
            <div class="relative z-10">
                <p class="text-indigo-300 text-xs">© {{ date('Y') }} StudentHub. Built for modern education.</p>
            </div>
        </div>

        {{-- Right form panel --}}
        <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 sm:px-10 lg:px-16">
            {{-- Mobile logo --}}
            <div class="lg:hidden flex items-center gap-2.5 mb-10">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
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
