@extends('layouts.main')

@section('title', 'Pengaturan Profil')

@section('content')
    <div class="py-12">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-8">
                Pengaturan Profil
            </h1>
            
            {{-- LAYOUT GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- KOLOM KIRI (Update Profile) --}}
                <div class="lg:col-span-2">
                    <div class="h-full p-8 bg-white shadow-xl shadow-slate-200/50 border border-slate-100 sm:rounded-[2.5rem] transition-all hover:shadow-2xl">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN (Password & Delete) --}}
                <div class="space-y-8">
                    
                    {{-- Update Password --}}
                    <div class="p-8 bg-white shadow-xl shadow-slate-200/50 border border-slate-100 sm:rounded-[2.5rem] transition-all hover:shadow-2xl">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    {{-- Delete Account --}}
                    <div class="p-8 bg-white shadow-xl shadow-slate-200/50 border border-red-50 sm:rounded-[2.5rem] transition-all">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>

            </div>
            
        </div>
    </div>
@endsection