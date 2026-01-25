@extends('layouts.admin')

@section('title', 'Aturan Penilaian')
@section('page_title', 'Aturan Penilaian')

@section('content')

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="text-lg font-extrabold tracking-tight text-slate-900">Aturan Penilaian</h2>
            <p class="mt-1 text-sm text-slate-600">
                Fitur ini sudah dihapus. Nilai sekarang dihitung otomatis berdasarkan hari magang (tanggal mulai/selesai).
            </p>
        </div>
        <div class="p-6">
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                Kembali ke User Management
            </a>
        </div>
    </section>
