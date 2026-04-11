@extends('layouts.dashboard')

@section('title', 'Informasi Murid & Orang Tua')
@section('page_title', 'Informasi Murid & Orang Tua')

@section('content')
    @php
        $registration = $approvedRegistration ?? $pendingRegistration ?? null;
    @endphp

    @if(!$registration)
        <div class="card" style="max-width: 900px; margin: 0 auto;">
            <h2 style="margin-bottom: 12px;">Informasi Murid & Orang Tua</h2>
            <p style="color: var(--gray); font-weight: 700;">Belum ada pendaftaran pada akun Anda.</p>
            <div style="margin-top: 14px;">
                <a href="{{ route('dashboard') }}#registration-form" class="btn-primary">📝 Mulai Pendaftaran</a>
                <a href="{{ route('dashboard') }}" class="btn-secondary">⬅️ Kembali</a>
            </div>
        </div>
    @else
        @include('components.dashboard.guest-parent-info', [
            'registration' => $registration,
            'studentInfo' => $studentInfo ?? null,
        ])
    @endif
@endsection
