<x-app-layout>
    <x-slot name="header">
        {{ __('Pengaturan Profil') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="p-8 bg-white shadow-xl shadow-slate-200/50 border border-slate-100 sm:rounded-[2.5rem] transition-all hover:shadow-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-8 bg-white shadow-xl shadow-slate-200/50 border border-slate-100 sm:rounded-[2.5rem] transition-all hover:shadow-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-8 bg-white shadow-xl shadow-slate-200/50 border border-red-50 sm:rounded-[2.5rem] transition-all">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>