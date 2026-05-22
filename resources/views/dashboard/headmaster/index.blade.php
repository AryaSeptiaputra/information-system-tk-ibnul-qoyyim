@extends('layouts.dashboard')

@section('title', 'Dashboard - TK Ibnul Qoyyim')
@section('page_title', 'Dashboard')

@php
    $rp = static fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
@endphp

@section('content')

<x-ui.page-header :title="'Dashboard · ' . $now->translatedFormat('F Y')" />

{{-- KPI Cards --}}
<div class="ui-stat-card-grid" style="margin-bottom: var(--ui-space-lg);">
    <x-ui.stat-card label="Total Murid Aktif" :value="$kpi['students']" hint="status aktif" />
    <x-ui.stat-card label="Total Guru" :value="$kpi['teachers']" hint="status aktif" />
    <x-ui.stat-card label="Kelas Aktif" :value="$kpi['classes']" hint="total kelas terdaftar" />
    <x-ui.stat-card label="Tagihan Belum Lunas" :value="$kpi['unpaid_invoices']" hint="pending / failed" />
</div>

{{-- Charts --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: var(--ui-space-md);">
    <div class="ui-section">
        <h3 class="ui-section__title">Kehadiran Murid Bulan Ini</h3>
        <div style="position: relative; height: 280px;">
            <canvas id="donutChart"></canvas>
        </div>
    </div>

    <div class="ui-section">
        <h3 class="ui-section__title">Trend Total Honor (6 Bulan)</h3>
        <div style="position: relative; height: 280px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>
</div>

<script>
    const DONUT = @json($donutData);
    const TREND = @json($trendData);

    function formatRp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID', { maximumFractionDigits: 0 });
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof window.Chart === 'undefined') {
            document.getElementById('donutChart').parentElement.innerHTML =
                '<p class="ui-form-error">Chart.js belum termuat. Jalankan <code>npm install</code> &amp; <code>npm run build</code>.</p>';
            return;
        }

        // Donut kehadiran
        const donutCtx = document.getElementById('donutChart').getContext('2d');
        new window.Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: DONUT.labels,
                datasets: [{
                    data: DONUT.data,
                    backgroundColor: [
                        'rgba(46, 204, 113, 0.7)',
                        'rgba(33, 150, 243, 0.7)',
                        'rgba(255, 152, 0, 0.7)',
                        'rgba(198, 40, 40, 0.7)',
                    ],
                    borderWidth: 1,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right' } },
            },
        });

        // Trend honor
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new window.Chart(trendCtx, {
            type: 'line',
            data: {
                labels: TREND.labels,
                datasets: [{
                    label: 'Total Honor',
                    data: TREND.values,
                    borderColor: 'rgb(46, 204, 113)',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    tension: 0.3,
                    fill: true,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: v => 'Rp ' + Number(v).toLocaleString('id-ID', { notation: 'compact' }) },
                    },
                },
                plugins: {
                    tooltip: { callbacks: { label: ctx => `${ctx.dataset.label}: ${formatRp(ctx.parsed.y)}` } },
                    legend: { display: false },
                },
            },
        });
    });
</script>

@endsection
