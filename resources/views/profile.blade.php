<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Profile Settings</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Manage your account information and security.</p>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-3xl">
        <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700/50 shadow-sm shadow-gray-100/50 dark:shadow-slate-900/50">
            <div class="max-w-xl">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700/50 shadow-sm shadow-gray-100/50 dark:shadow-slate-900/50">
            <div class="max-w-xl">
                <livewire:profile.update-password-form />
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700/50 shadow-sm shadow-gray-100/50 dark:shadow-slate-900/50">
            <div class="max-w-xl">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-app-layout>
