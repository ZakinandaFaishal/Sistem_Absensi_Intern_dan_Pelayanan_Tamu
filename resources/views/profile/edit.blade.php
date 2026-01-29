@extends('layouts.userLayout')

@section('title', 'Profil Saya')
@section('page_title', 'Profil')

@section('content')
    @php
        $user = request()->user();
    @endphp

    <div class="max-w-6xl mx-auto space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <div class="lg:col-span-7 space-y-6">
                @include('profile.partials.update-profile-and-password-form', ['user' => $user])
            </div>

            <div class="lg:col-span-5">
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
@endsection
