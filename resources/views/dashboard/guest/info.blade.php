@extends('layouts.dashboard')

@section('title', 'Profil & Info - TK Ibnul Qoyyim')
@section('page_title', 'Profil & Info')

@php
    $registration = $approvedRegistration ?? $pendingRegistration ?? null;
    $parentsData = $registration?->parents_data ?? [];
    $candidateData = $registration?->candidate_data ?? [];
    $studentsList = collect($students ?? []);
@endphp

@section('content')

<x-ui.page-header title="Profil & Info">
    <x-slot:action>
        <a href="{{ route('profile.edit') }}" class="ui-btn ui-btn--secondary">Edit Akun</a>
    </x-slot:action>
</x-ui.page-header>

@if(session('status'))
    <x-ui.toast variant="success">{{ session('status') }}</x-ui.toast>
@endif

{{-- Data Akun --}}
<div class="ui-section">
    <h3 class="ui-section__title">Data Akun</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: var(--ui-space-md);">
        <div>
            <div class="ui-stat-card__label">Nama</div>
            <div>{{ $user->name ?? '-' }}</div>
        </div>
        <div>
            <div class="ui-stat-card__label">Email</div>
            <div>{{ $user->email ?? '-' }}</div>
        </div>
        <div>
            <div class="ui-stat-card__label">Telepon</div>
            <div>{{ $user->phone_num ?? '-' }}</div>
        </div>
    </div>
</div>

{{-- Data Orangtua / Wali --}}
@if($registration)
    <div class="ui-section">
        <h3 class="ui-section__title">Data Orangtua / Wali</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: var(--ui-space-md);">
            <div>
                <div class="ui-stat-card__label">Nama Ayah</div>
                <div>{{ $parentsData['father_name'] ?? '-' }}</div>
                <div class="ui-stat-card__hint" style="margin-top: 4px;">{{ $parentsData['father_occupation'] ?? '-' }}</div>
            </div>
            <div>
                <div class="ui-stat-card__label">Telepon Ayah</div>
                <div>{{ $parentsData['father_phone_num'] ?? '-' }}</div>
            </div>
            <div>
                <div class="ui-stat-card__label">Alamat Ayah</div>
                <div>{{ $parentsData['father_address'] ?? '-' }}</div>
            </div>
            <div>
                <div class="ui-stat-card__label">Nama Ibu</div>
                <div>{{ $parentsData['mother_name'] ?? '-' }}</div>
                <div class="ui-stat-card__hint" style="margin-top: 4px;">{{ $parentsData['mother_occupation'] ?? '-' }}</div>
            </div>
            <div>
                <div class="ui-stat-card__label">Telepon Ibu</div>
                <div>{{ $parentsData['mother_phone_num'] ?? '-' }}</div>
            </div>
            <div>
                <div class="ui-stat-card__label">Alamat Ibu</div>
                <div>{{ $parentsData['mother_address'] ?? '-' }}</div>
            </div>
        </div>
    </div>
@endif

{{-- Anak yang Terdaftar --}}
<div class="ui-section">
    <h3 class="ui-section__title">Anak Saya</h3>
    @if($studentsList->isEmpty() && !$registration)
        <x-ui.empty-state icon="📭" message="Belum ada pendaftaran anak pada akun Anda.">
            <x-slot:action>
                <a href="{{ route('registration.create') }}" class="ui-btn ui-btn--primary">📝 Mulai Pendaftaran</a>
            </x-slot:action>
        </x-ui.empty-state>
    @elseif($studentsList->isEmpty())
        {{-- ada pendaftaran tapi belum jadi student aktif --}}
        <div class="ui-table-wrapper" style="box-shadow: none; border: none;">
            <table class="ui-table">
                <tbody>
                    <tr>
                        <td><strong>{{ $candidateData['name'] ?? '-' }}</strong></td>
                        <td>Grup {{ $registration->group ?? '-' }}</td>
                        <td><x-ui.badge variant="warning">Pendaftaran: {{ $registration->status ?? '-' }}</x-ui.badge></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @else
        <div class="ui-table-wrapper" style="box-shadow: none; border: none;">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Grup</th>
                        <th>Gender</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentsList as $s)
                        @php
                            $sv = $s->status ?? '-';
                            $sb = match (strtolower($sv)) {
                                'aktif' => 'success',
                                'rejected' => 'danger',
                                default => 'neutral',
                            };
                        @endphp
                        <tr>
                            <td><strong>{{ $s->name ?? '-' }}</strong></td>
                            <td>{{ $s->group ?? '-' }}</td>
                            <td>{{ $s->gender ?? '-' }}</td>
                            <td><x-ui.badge :variant="$sb">{{ ucfirst($sv) }}</x-ui.badge></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Info Sekolah --}}
<div class="ui-section">
    <h3 class="ui-section__title">Info Sekolah</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: var(--ui-space-md);">
        <div>
            <div class="ui-stat-card__label">Nama Sekolah</div>
            <div>TK Ibnul Qoyyim Sulawesi</div>
        </div>
        <div>
            <div class="ui-stat-card__label">Sistem</div>
            <div>Sistem Informasi Sekolah</div>
            <div class="ui-stat-card__hint" style="margin-top: 4px;">Untuk pertanyaan terkait sekolah, hubungi pihak admin/bendahara.</div>
        </div>
    </div>
</div>

@endsection
