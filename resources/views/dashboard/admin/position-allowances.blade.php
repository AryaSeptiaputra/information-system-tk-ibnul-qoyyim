@extends('layouts.dashboard')

@section('title', 'Tunjangan Posisi - TK Ibnul Qoyyim')
@section('page_title', 'Tunjangan Posisi')

@php
    $rp = static fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
@endphp

@section('content')

<x-ui.page-header title="Tunjangan Posisi">
    <x-slot:action>
        <a href="{{ route('admin.positions.index', ['tab' => 'nominal']) }}" class="ui-btn ui-btn--ghost">
            ← Kembali ke Posisi & Tunjangan
        </a>
        <button type="button" class="ui-btn ui-btn--primary" onclick="loadCreateAllowance()" style="margin-left: var(--ui-space-sm);">
            + Tambah Tunjangan
        </button>
    </x-slot:action>
</x-ui.page-header>

@if(session('success'))
    <x-ui.toast variant="success">{{ session('success') }}</x-ui.toast>
@endif

<div class="ui-toolbar">
    <form method="GET" action="{{ route('admin.position-allowances.index') }}" class="ui-toolbar__search">
        <input type="search" name="search" value="{{ $search }}" class="ui-input" placeholder="Cari posisi / jenis tunjangan...">
    </form>
</div>

@if(($allowances?->isEmpty()) ?? true)
    <x-ui.empty-state icon="💼" message="Belum ada tunjangan posisi." />
@else
    <div class="ui-table-wrapper">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Posisi</th>
                    <th>Jenis Tunjangan</th>
                    <th>Nominal</th>
                    <th>Berlaku Dari</th>
                    <th>Sampai</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($allowances as $pa)
                    <tr>
                        <td><strong>{{ $pa->position?->name ?? '-' }}</strong></td>
                        <td>{{ $pa->allowanceType?->name ?? '-' }}</td>
                        <td>{{ $rp($pa->amount) }}</td>
                        <td>{{ $pa->effective_from?->format('d M Y') ?? '-' }}</td>
                        <td>{{ $pa->effective_to?->format('d M Y') ?? '—' }}</td>
                        <td>
                            <div class="ui-table__actions">
                                <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" onclick="loadEditAllowance({{ $pa->id_position_allowance }})">Edit</button>
                                <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" style="color: var(--color-danger);" onclick="confirmDeleteAllowance('{{ route('admin.position-allowances.destroy', $pa) }}', '{{ addslashes($pa->position?->name ?? '-') }} · {{ addslashes($pa->allowanceType?->name ?? '-') }}')">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('components.dashboard.admin.pagination-controls', [
        'items' => $allowances,
        'search' => $search,
        'per_page' => $per_page ?? 10,
    ])
@endif

<x-ui.modal name="pa-modal" title="Tunjangan Posisi" maxWidth="560px">
    <div id="pa-modal-content">
        <p class="ui-stat-card__hint">Memuat...</p>
    </div>
</x-ui.modal>

<script>
    function loadCreateAllowance() {
        window.loadFormIntoModal('{{ route('admin.position-allowances.create') }}', 'pa-modal-content', 'pa-modal');
    }
    function loadEditAllowance(id) {
        window.loadFormIntoModal(`/admin/position-allowances/${id}/edit`, 'pa-modal-content', 'pa-modal');
    }
    function confirmDeleteAllowance(url, label) {
        if (!confirm(`Hapus tunjangan "${label}"?`)) return;
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch(url, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token }})
            .then(r => r.json()).then(() => location.reload())
            .catch(() => alert('Gagal menghapus.'));
    }
</script>

@endsection
