@extends('layouts.dashboard')

@section('title', 'Laporan Bulanan - TK Ibnul Qoyyim')
@section('page_title', 'Laporan Bulanan')

@php
    $rp = static fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
@endphp

@section('content')

<x-ui.page-header title="Laporan Bulanan">
    <x-slot:action>
        <a href="{{ route('admin.headmaster.reports.export', ['bulan' => $periode->format('Y-m')]) }}" class="ui-btn ui-btn--secondary">
            Export CSV
        </a>
    </x-slot:action>
</x-ui.page-header>

<div class="ui-toolbar">
    <form method="GET" action="{{ route('admin.headmaster.reports') }}">
        <input type="month" name="bulan" value="{{ $periode->format('Y-m') }}" class="ui-input" onchange="this.form.submit()" style="width: auto;">
    </form>
    <div style="color: var(--color-muted); font-size: var(--ui-font-sm);">
        Periode: <strong style="color: var(--color-text);">{{ $periodeLabel }}</strong>
    </div>
</div>

{{-- Ringkasan Keuangan --}}
<div class="ui-section">
    <h3 class="ui-section__title">Ringkasan Keuangan</h3>
    <div class="ui-stat-card-grid">
        <x-ui.stat-card label="Dana Masuk" :value="$rp($summary['fund_in'])" hint="bulan ini" />
        <x-ui.stat-card label="Dana Keluar" :value="$rp($summary['fund_out'])" hint="bulan ini" />
        <x-ui.stat-card
            label="Net"
            :value="$rp($summary['fund_net'])"
            :hint="$summary['fund_net'] >= 0 ? 'surplus' : 'defisit'"
        />
        <x-ui.stat-card label="Saldo Akhir" :value="$rp($summary['fund_balance'])" hint="total semua sumber" />
    </div>
</div>

{{-- Ringkasan Honor --}}
<div class="ui-section">
    <h3 class="ui-section__title">Ringkasan Honor Guru</h3>
    <div class="ui-table-wrapper" style="box-shadow: none; border: none;">
        <table class="ui-table">
            <tbody>
                <tr>
                    <td><strong>Total Honor Periode</strong></td>
                    <td style="text-align: right;">{{ $rp($summary['honor_total']) }}</td>
                </tr>
                <tr>
                    <td>Sudah Dibayar</td>
                    <td style="text-align: right;">
                        <span style="color: var(--color-primary-dark);">{{ $rp($summary['honor_paid']) }}</span>
                        <span class="ui-stat-card__hint">({{ $summary['honor_paid_count'] }} guru)</span>
                    </td>
                </tr>
                <tr>
                    <td>Belum Dibayar</td>
                    <td style="text-align: right;">
                        <span style="color: var(--color-warning);">{{ $rp($summary['honor_unpaid']) }}</span>
                        <span class="ui-stat-card__hint">({{ $summary['honor_unpaid_count'] }} guru)</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Ringkasan Kehadiran Guru --}}
<div class="ui-section">
    <h3 class="ui-section__title">Kehadiran Guru</h3>

    @if(count($attendanceTable) === 0)
        <x-ui.empty-state message="Belum ada data guru." />
    @else
        <div class="ui-table-wrapper" style="box-shadow: none; border: none;">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Guru</th>
                        <th style="text-align: center;">Hadir</th>
                        <th style="text-align: center;">Izin</th>
                        <th style="text-align: center;">Sakit</th>
                        <th style="text-align: center;">Alpa</th>
                        <th style="text-align: center;">Telat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendanceTable as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td style="text-align: center;">{{ $row['hadir'] }}</td>
                            <td style="text-align: center;">{{ $row['izin'] }}</td>
                            <td style="text-align: center;">{{ $row['sakit'] }}</td>
                            <td style="text-align: center;">{{ $row['alpa'] }}</td>
                            <td style="text-align: center;">
                                @if($row['late'] > 0)
                                    <x-ui.badge variant="warning">{{ $row['late'] }}</x-ui.badge>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
