<x-guest-layout>
    <div class="mb-8">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center mb-5">
            <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Reset your password</h1>
        <p class="mt-1.5 text-sm text-gray-500">
            Enter your email and we'll send a reset link.
        </p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input
                id="email"
                class="block w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                placeholder="you@example.com"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <x-primary-button class="w-full justify-center py-3 text-sm">
            {{ __('Send reset link') }}
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        Remembered it?
        <a href="{{ route('login') }}"
           class="font-semibold text-indigo-600 hover:text-indigo-700 transition">
            Back to sign in
        </a>
    </p>
</x-guest-layout>
