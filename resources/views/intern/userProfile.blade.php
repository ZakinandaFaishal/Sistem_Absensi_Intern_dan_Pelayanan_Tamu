@extends('layouts.userLayout')

@section('title', 'Profil Akun - User Panel')
@section('page_title', 'Profil')

@section('content')
@php
    $user = Auth::user();
@endphp

<div class="relative">

    {{-- Header --}}
    <div class="relative rounded-3xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-xl p-6 sm:p-7">
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
                <div class="h-12 w-12 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center shadow">
                    <span class="text-xl font-extrabold">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </span>
                </div>
                <div class="leading-tight">
                    <p class="text-sm font-semibold text-white">{{ $user->name }}</p>
                    <p class="text-xs text-white/60">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        <div class="mt-6 h-[2px] w-full rounded-full bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>

        {{-- Alerts --}}
        <div class="mt-5 space-y-3">
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-300/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                    ✅ {{ session('status') }}
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
    </div>

    {{-- Content --}}
    <div class="relative mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Kiri: Profile + Password --}}
        <div class="lg:col-span-7 space-y-6">
            {{-- gunakan partial bawaan --}}
            @include('profile.partials.update-profile-information-form', ['user' => $user])

            @include('profile.partials.update-password-form')
        </div>

        {{-- Kanan: Delete user --}}
        <div class="lg:col-span-5">
            @include('profile.partials.delete-user-form')
        </div>

    </div>
</div>
@endsection
