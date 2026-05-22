@extends('layouts.dashboard')

@section('title', 'Honor Saya - TK Ibnul Qoyyim')
@section('page_title', 'Honor Saya')

@php
    $rp = static fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $monthNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $periodLabel = static function ($row) use ($monthNames) {
        if ($row?->period_start && $row?->period_end) {
            return $row->period_start->format('d M') . ' – ' . $row->period_end->format('d M Y');
        }
        $m = (int) ($row?->month ?? 0);
        $y = (int) ($row?->year ?? 0);
        return $m && $y ? ($monthNames[$m] . ' ' . $y) : '-';
    };
@endphp

@section('content')

<x-ui.page-header title="Honor Saya" />

@if(!$teacher)
    <x-ui.empty-state message="Akun Anda belum tertaut ke data guru. Hubungi admin." />
@else
    {{-- Bulan berjalan (StatCard besar) --}}
    <div class="ui-section">
        @if($myHonor)
            @php
                $isPaid = (bool) $myHonor->payment_date;
            @endphp
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: var(--ui-space-md);">
                <div>
                    <div class="ui-stat-card__label">Bulan Berjalan ({{ $periodLabel($myHonor) }})</div>
                    <div style="font-size: var(--ui-font-2xl); font-weight: 700; color: var(--color-primary-dark); margin-top: var(--ui-space-xs);">
                        {{ $rp($myHonor->amount) }}
                    </div>
                    <div class="ui-stat-card__hint" style="margin-top: var(--ui-space-xs);">
                        {{ (int) $myHonor->attendance_count }} hadir ·
                        {{ (int) $myHonor->permission_count }} izin ·
                        {{ (int) $myHonor->sickness_count }} sakit ·
                        {{ (int) $myHonor->late_count }} telat
                    </div>
                </div>
                <div>
                    @if($isPaid)
                        <x-ui.badge variant="success">Paid {{ $myHonor->payment_date?->format('d M Y') }}</x-ui.badge>
                    @else
                        <x-ui.badge variant="warning">Unpaid</x-ui.badge>
                    @endif
                </div>
            </div>
        @else
            <div class="ui-stat-card__label">Bulan Berjalan</div>
            <div style="font-size: var(--ui-font-lg); color: var(--color-muted); margin-top: var(--ui-space-xs);">
                Honor bulan ini belum di-generate oleh admin/bendahara.
            </div>
        @endif
    </div>

    {{-- Riwayat pencairan --}}
    <div class="ui-section">
        <h3 class="ui-section__title">Riwayat Pencairan</h3>

        @if(!$honors || $honors->total() === 0)
            <x-ui.empty-state icon="📭" message="Belum ada riwayat honor." />
        @else
            <div class="ui-table-wrapper" style="box-shadow: none; border: none;">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th>Hadir</th>
                            <th>Nominal</th>
                            <th>Tgl Bayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($honors as $h)
                            @php
                                $isPaid = (bool) $h->payment_date;
                            @endphp
                            <tr>
                                <td>{{ $periodLabel($h) }}</td>
                                <td>{{ (int) ($h->attendance_count ?? 0) }}</td>
                                <td><strong>{{ $rp($h->amount) }}</strong></td>
                                <td>{{ $h->payment_date?->format('d M Y') ?? '-' }}</td>
                                <td>
                                    @if($isPaid)
                                        <x-ui.badge variant="success">Paid</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="warning">Unpaid</x-ui.badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @include('components.dashboard.admin.pagination-controls', [
                'items' => $honors,
                'per_page' => $per_page ?? 10,
            ])
        @endif
    </div>
@endif

@endsection
