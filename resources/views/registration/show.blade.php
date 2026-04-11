@extends('layouts.dashboard')

@section('title', 'Detail Pendaftaran')
@section('page_title', 'Detail Pendaftaran')

@section('content')
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <h2 style="margin-bottom: 16px;">Status: {{ strtoupper(str_replace('_', ' ', $registration->status ?? '-')) }}</h2>

        @php
            $candidate = $registration->candidate_data ?? [];
            $parents = $registration->parents_data ?? [];

            $candidateBirthDateRaw = $candidate['birth_date'] ?? null;
            $candidateBirthDateLabel = '-';
            if ($candidateBirthDateRaw) {
                if (is_string($candidateBirthDateRaw)) {
                    $candidateBirthDateLabel = trim(explode(' ', $candidateBirthDateRaw)[0]);
                } elseif (is_object($candidateBirthDateRaw) && method_exists($candidateBirthDateRaw, 'format')) {
                    $candidateBirthDateLabel = $candidateBirthDateRaw->format('Y-m-d');
                } else {
                    $candidateBirthDateLabel = (string)$candidateBirthDateRaw;
                }
            }
        @endphp

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <h3>Data Calon Siswa</h3>
                <div>Nama: {{ $candidate['name'] ?? '-' }}</div>
                <div>TTL: {{ $candidate['birth_place'] ?? '-' }}, {{ $candidateBirthDateLabel }}</div>
                <div>Gender: {{ $candidate['gender'] ?? '-' }}</div>
                <div>Kelompok: {{ $registration->group ?? '-' }}</div>
            </div>
            <div>
                <h3>Data Orang Tua</h3>
                <div>Ayah: {{ $parents['father_name'] ?? '-' }} ({{ $parents['father_phone'] ?? '-' }})</div>
                <div>Ibu: {{ $parents['mother_name'] ?? '-' }} ({{ $parents['mother_phone'] ?? '-' }})</div>
            </div>
        </div>

        <hr style="margin: 20px 0;" />

        @if(($registration->reject_reason ?? null))
            <div style="margin-top: 16px; color: #b91c1c; font-weight: 600;">Alasan ditolak: {{ $registration->reject_reason }}</div>
        @endif

        <div style="margin-top: 20px;">
            <a href="{{ route('dashboard') }}" class="btn-primary">Kembali ke Dashboard</a>
        </div>
    </div>
@endsection
