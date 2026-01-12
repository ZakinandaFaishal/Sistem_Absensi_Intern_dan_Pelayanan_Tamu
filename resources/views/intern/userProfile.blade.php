@extends('layouts.userLayout')

@section('title', 'Profil Akun - User Panel')
@section('page_title', 'Profil')

@section('content')
    @php
        $user = Auth::user();
    @endphp

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Header --}}
        <section class="rounded-3xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-xl p-6 sm:p-7">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="min-w-0">
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white drop-shadow">
                        Profil Akun
                    </h2>
                    <p class="mt-1 text-sm text-white/70">
                        Kelola informasi profil, password, dan penghapusan akun.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <div
                        class="h-12 w-12 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center shadow">
                        <span class="text-xl font-extrabold text-white">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </span>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-semibold text-white">{{ $user->name }}</p>
                        <p class="text-xs text-white/60">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 h-[2px] w-full rounded-full bg-gradient-to-r from-transparent via-white/30 to-transparent">
            </div>

            {{-- Alerts (opsional, kalau kamu masih mau tampilkan di sini) --}}
            <div class="mt-5 space-y-3">
                @if (session('status'))
                    <div
                        class="rounded-2xl border border-emerald-300/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                        <x-icon name="check-circle" class="h-5 w-5" /> {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-2xl border border-rose-300/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">
                        <div class="font-semibold">Terjadi kesalahan:</div>
                        <ul class="list-disc pl-5 mt-1 space-y-0.5">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </section>

        {{-- Content (sejajar ke bawah) --}}
        <section class="space-y-6">

            {{-- Profile info --}}
            @include('profile.partials.update-profile-information-form', ['user' => $user])

            {{-- Update password --}}
            @include('profile.partials.update-password-form')

            {{-- Delete user --}}
            @include('profile.partials.delete-user-form')

        </section>

    </div>
@endsection
