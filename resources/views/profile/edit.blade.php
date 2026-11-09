<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
            <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 font-medium">Profile Settings</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-4">
        <div class="mb-2">
            <h1 class="text-xl font-bold text-gray-900">Profile Settings</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage your account details and security.</p>
        </div>

        {{-- Profile Info --}}
        <div class="card p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Password --}}
        <div class="card p-6">
            @include('profile.partials.update-password-form')
        </div>

        {{-- Delete Account --}}
        <div class="card p-6 border-red-100">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
