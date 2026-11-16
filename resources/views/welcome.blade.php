<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <title>StudentHub — School Management System for Students, Teachers &amp; Administration</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-gray-900">

    {{-- Navigation --}}
    <nav class="fixed top-0 inset-x-0 z-50 bg-white border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-md bg-indigo-700 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                    </svg>
                </div>
                <span class="text-lg font-bold tracking-tight">StudentHub</span>
            </a>

            @if (Route::has('login'))
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary btn-sm">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                            Sign in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary btn-sm">
                                Get started
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    {{-- Hero --}}
    <section class="pt-36 pb-20 px-6">
        <div class="max-w-3xl mx-auto text-center">
            <p class="text-xs font-semibold uppercase tracking-widest text-indigo-700 mb-4">
                Internal school management platform
            </p>
            <h1 class="text-4xl sm:text-5xl font-bold tracking-tight leading-tight mb-6">
                One platform for our school's<br>academic operations.
            </h1>
            <p class="text-lg text-gray-600 leading-relaxed max-w-2xl mx-auto mb-9">
                StudentHub helps our school's administration, teachers, and students manage student records,
                attendance, examinations, and academic performance — all in one place.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary px-6 py-3">
                        Create an account
                    </a>
                @endif
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn-secondary px-6 py-3">
                        Sign in
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="border-t border-gray-200 bg-gray-50 py-20 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="text-3xl font-bold tracking-tight">
                    Everything our school needs, in one place
                </h2>
                <p class="text-gray-600 mt-3 leading-relaxed">
                    From day-to-day attendance to end-of-term results — StudentHub simplifies daily school administration.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $features = [
                        [
                            'title' => 'Student Records',
                            'desc'  => 'Maintain complete, searchable student profiles — codes, grades, classes, and enrollment details.',
                            'svg'   => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
                        ],
                        [
                            'title' => 'Attendance Tracking',
                            'desc'  => 'Mark and review daily attendance per class. Filter by date, grade, or class in seconds.',
                            'svg'   => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z',
                        ],
                        [
                            'title' => 'Examinations & Grades',
                            'desc'  => 'Create exams, enter grades, and track academic performance across subjects and classes.',
                            'svg'   => 'M16.5 8.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v8.25A2.25 2.25 0 006 16.5h2.25m8.25-8.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-7.5A2.25 2.25 0 018.25 18v-1.5m8.25-8.25h-6a2.25 2.25 0 00-2.25 2.25v6',
                        ],
                        [
                            'title' => 'Subjects Management',
                            'desc'  => 'Organize subjects, assign teachers, and keep curriculum information up to date.',
                            'svg'   => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25',
                        ],
                        [
                            'title' => 'School Statistics',
                            'desc'  => 'Clear reports on enrollment, attendance, grades, and class performance for informed decisions.',
                            'svg'   => 'M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z',
                        ],
                        [
                            'title' => 'Teacher Management',
                            'desc'  => 'Approve teacher accounts and keep staff information organized for our school.',
                            'svg'   => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
                        ],
                    ];
                @endphp

                @foreach ($features as $feature)
                    <div class="card p-6">
                        <div class="w-10 h-10 rounded-md bg-indigo-50 border border-indigo-100 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-indigo-700" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feature['svg'] }}"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-1.5">{{ $feature['title'] }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $feature['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-gray-200 py-8 px-6">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-sm text-gray-500">© {{ date('Y') }} StudentHub — Internal school management platform.</p>
            <p class="text-sm text-gray-400">Helping our school stay organized and connected.</p>
        </div>
    </footer>

</body>
</html>
