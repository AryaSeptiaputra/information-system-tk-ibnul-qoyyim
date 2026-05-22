@extends('layouts.dashboard')

@section('title', 'Master Payment - TK Ibnul Qoyyim')
@section('page_title', 'Master Payment')

@php
    $rp = static fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');

    $periodLabel = static fn (?string $m) => match ($m) {
        'one_time' => 'Sekali',
        'monthly' => 'Bulanan',
        'semester' => 'Semester',
        'yearly' => 'Tahunan',
        default => ucfirst((string) $m),
    };
@endphp

@section('content')

<x-ui.page-header title="Master Payment">
    <x-slot:action>
        <a href="{{ route('admin.payments.export', request()->query()) }}" class="ui-btn ui-btn--secondary">Export CSV</a>
        <button type="button" class="ui-btn ui-btn--primary" onclick="loadCreatePayment()" style="margin-left: var(--ui-space-sm);">+ Tambah</button>
    </x-slot:action>
</x-ui.page-header>

@if(session('success'))
    <x-ui.toast variant="success">{{ session('success') }}</x-ui.toast>
@endif

<div class="ui-toolbar">
    <form method="GET" action="{{ route('admin.payments.index') }}" class="ui-toolbar__search">
        <input type="hidden" name="period_mode" value="{{ $period_mode ?? 'all' }}">
        <input type="hidden" name="status" value="{{ $status ?? 'all' }}">
        <input type="search" name="search" value="{{ $search }}" class="ui-input" placeholder="Cari nama jenis tagihan...">
    </form>
    <div class="ui-toolbar__filters">
        <form method="GET" action="{{ route('admin.payments.index') }}">
            <input type="hidden" name="search" value="{{ $search }}">
            <input type="hidden" name="status" value="{{ $status ?? 'all' }}">
            <select name="period_mode" class="ui-select" onchange="this.form.submit()">
                <option value="all" @selected(($period_mode ?? 'all') === 'all')>Semua Periode</option>
                <option value="one_time" @selected(($period_mode ?? '') === 'one_time')>Sekali</option>
                <option value="monthly" @selected(($period_mode ?? '') === 'monthly')>Bulanan</option>
                <option value="semester" @selected(($period_mode ?? '') === 'semester')>Semester</option>
                <option value="yearly" @selected(($period_mode ?? '') === 'yearly')>Tahunan</option>
            </select>
        </form>
        <form method="GET" action="{{ route('admin.payments.index') }}">
            <input type="hidden" name="search" value="{{ $search }}">
            <input type="hidden" name="period_mode" value="{{ $period_mode ?? 'all' }}">
            <select name="status" class="ui-select" onchange="this.form.submit()">
                <option value="all" @selected(($status ?? 'all') === 'all')>Semua Status</option>
                <option value="active" @selected(($status ?? '') === 'active')>Aktif</option>
                <option value="inactive" @selected(($status ?? '') === 'inactive')>Nonaktif</option>
            </select>
        </form>
    </div>
</div>

@if(($payments?->isEmpty()) ?? true)
    <x-ui.empty-state message="Belum ada master payment." />
@else
    <div class="ui-table-wrapper">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Nama Tagihan</th>
                    <th>Jenis</th>
                    <th>Periode</th>
                    <th>Default Amount</th>
                    <th>Komponen</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $p)
                    @php
                        $components = is_array($p->detail_fee_template) ? count($p->detail_fee_template) : 0;
                    @endphp
                    <tr>
                        <td><strong>{{ $p->name ?? '-' }}</strong></td>
                        <td style="color: var(--color-muted); font-family: monospace;">{{ $p->jenis_payment ?? '-' }}</td>
                        <td>{{ $periodLabel($p->period_mode) }}</td>
                        <td>{{ $rp($p->default_amount ?? 0) }}</td>
                        <td>
                            @if($components > 0)
                                <x-ui.badge variant="info">{{ $components }} komponen</x-ui.badge>
                            @else
                                <span class="ui-stat-card__hint">monolitik</span>
                            @endif
                        </td>
                        <td>
                            @if($p->is_active)
                                <x-ui.badge variant="success">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                            @endif
                        </td>
                        <td>
                            <div class="ui-table__actions">
                                <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" onclick="loadDetailPayment({{ $p->id_payment }})">Lihat</button>
                                <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" onclick="loadEditPayment({{ $p->id_payment }})">Edit</button>
                                <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" style="color: var(--color-danger);" onclick="confirmDeletePayment('{{ route('admin.payments.destroy', $p) }}', '{{ addslashes($p->name ?? '-') }}')">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('components.dashboard.admin.pagination-controls', [
        'items' => $payments,
        'search' => $search,
        'period_mode' => $period_mode ?? 'all',
        'status' => $status ?? 'all',
        'per_page' => $per_page ?? 10,
    ])
@endif

<x-ui.modal name="payment-modal" title="Master Payment" maxWidth="640px">
    <div id="payment-modal-content">
        <p class="ui-stat-card__hint">Memuat...</p>
    </div>
</x-ui.modal>

<script>
    function loadCreatePayment() {
        window.loadFormIntoModal('{{ route('admin.payments.create') }}', 'payment-modal-content', 'payment-modal');
    }
    function loadEditPayment(id) {
        window.loadFormIntoModal(`/admin/payments/${id}/edit`, 'payment-modal-content', 'payment-modal');
    }
    function loadDetailPayment(id) {
        window.loadDetailIntoModal(`/admin/payments/${id}`, 'payment-modal-content', 'payment-modal');
    }
    function confirmDeletePayment(url, label) {
        if (!confirm(`Hapus master payment "${label}"?`)) return;
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch(url, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token }})
            .then(r => r.json()).then(() => location.reload())
            .catch(() => alert('Gagal menghapus.'));
    }
</script>

@include('components.dashboard.admin.admin-scripts')

@endsection
