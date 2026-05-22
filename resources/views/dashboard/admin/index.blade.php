@extends('layouts.dashboard')

@section('title', 'Dashboard - TK Ibnul Qoyyim')
@section('page_title', 'Dashboard')

@php
    $s = $stats ?? [];
    $currentRole = auth()->user()?->role ?? null;
    $isTeacher = $currentRole === 'teacher';
@endphp

@section('content')

<x-ui.page-header :title="'Dashboard · Selamat datang, ' . (auth()->user()?->name ?? 'User')" />

{{-- Stat cards utama --}}
<div class="ui-stat-card-grid" style="margin-bottom: var(--ui-space-lg);">
    <x-ui.stat-card label="Total Pengguna" :value="(int)($s['total_users'] ?? 0)" hint="semua role" />
    <x-ui.stat-card label="Total Guru" :value="(int)($s['total_teachers'] ?? 0)" hint="terdaftar" />
    <x-ui.stat-card label="Total Murid" :value="(int)($s['total_students'] ?? 0)" hint="terdaftar" />
    <x-ui.stat-card label="Total Kelas" :value="(int)($s['total_classes'] ?? 0)" hint="kelas tersedia" />
</div>

{{-- Stat cards sekunder --}}
<div class="ui-stat-card-grid" style="margin-bottom: var(--ui-space-lg);">
    <x-ui.stat-card label="Pendaftaran Menunggu" :value="(int)($s['pending_registrations'] ?? 0)" hint="perlu review" />
    <x-ui.stat-card label="Pendaftaran Aktif" :value="(int)($s['approved_registrations'] ?? 0)" hint="status active" />
    <x-ui.stat-card label="Pengguna Staf" :value="(int)($s['active_users'] ?? 0)" hint="non-guest" />
</div>

{{-- Recent registrations --}}
@if(($s['recent_registrations'] ?? collect())->count() > 0)
    <div class="ui-section">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--ui-space-md);">
            <h3 class="ui-section__title" style="margin: 0;">5 Pendaftaran Terbaru</h3>
            <a href="{{ route('admin.registrations.index') }}" class="ui-btn ui-btn--ghost">Lihat semua →</a>
        </div>

        <div class="ui-table-wrapper" style="box-shadow: none; border: none;">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Calon</th>
                        <th>Grup</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($s['recent_registrations'] as $reg)
                        @php
                            $statusBadge = match ($reg->status ?? '') {
                                'active' => 'success',
                                'pending', 'approved_awaiting_payment', 'pending_due' => 'warning',
                                'rejected' => 'danger',
                                default => 'neutral',
                            };
                            $candidateName = $reg->candidate_data['name'] ?? '-';
                        @endphp
                        <tr>
                            <td>{{ $reg->created_at?->format('d M Y') ?? '-' }}</td>
                            <td><strong>{{ $candidateName }}</strong></td>
                            <td>{{ $reg->group ?? '-' }}</td>
                            <td><x-ui.badge :variant="$statusBadge">{{ ucfirst($reg->status ?? '-') }}</x-ui.badge></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Teacher honor summary (hanya untuk role teacher) --}}
@if($isTeacher)
    @include('components.dashboard.admin.teacher-honor-summary', [
        'user' => $user ?? auth()->user(),
        'myTeacherDetail' => $myTeacherDetail ?? null,
        'myHonor' => $myHonor ?? null,
        'myHonorLatest' => $myHonorLatest ?? null,
        'myHonorList' => $myHonorList ?? collect(),
    ])
@endif

@endsection
