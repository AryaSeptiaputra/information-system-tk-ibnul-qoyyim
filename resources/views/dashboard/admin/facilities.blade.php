@extends('layouts.dashboard')

@section('title', 'Sarpras - TK Ibnul Qoyyim')
@section('page_title', 'Sarpras')

@section('content')

<x-ui.page-header title="Sarana & Prasarana">
    <x-slot:action>
        <button type="button" class="ui-btn ui-btn--primary" onclick="loadCreateFacility()">+ Tambah Barang</button>
    </x-slot:action>
</x-ui.page-header>

@if(session('success'))
    <x-ui.toast variant="success">{{ session('success') }}</x-ui.toast>
@endif

<div class="ui-toolbar">
    <form method="GET" action="{{ route('admin.facilities.index') }}" class="ui-toolbar__search">
        <input type="hidden" name="status" value="{{ $status }}">
        <input type="search" name="search" value="{{ $search }}" class="ui-input" placeholder="Cari barang...">
    </form>
    <div class="ui-toolbar__filters">
        <form method="GET" action="{{ route('admin.facilities.index') }}">
            <input type="hidden" name="search" value="{{ $search }}">
            <select name="status" class="ui-select" onchange="this.form.submit()">
                <option value="all" @selected($status === 'all')>Semua</option>
                <option value="active" @selected($status === 'active')>Aktif</option>
                <option value="inactive" @selected($status === 'inactive')>Nonaktif</option>
            </select>
        </form>
    </div>
</div>

@if(($facilities?->isEmpty()) ?? true)
    <x-ui.empty-state message="Belum ada data sarpras." />
@else
    <div class="ui-table-wrapper">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th>Kondisi</th>
                    <th>Tahun</th>
                    <th>Sumber Dana</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($facilities as $f)
                    @php
                        $conditionBadge = match ($f->condition) {
                            'Baik' => 'success',
                            'Rusak Ringan' => 'warning',
                            'Rusak Berat' => 'danger',
                            default => 'neutral',
                        };
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $f->name }}</strong>
                            @if(!$f->is_active)
                                <x-ui.badge variant="neutral" class="ms-1">Nonaktif</x-ui.badge>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ (int) ($f->quantity ?? 0) }}</td>
                        <td><x-ui.badge :variant="$conditionBadge">{{ $f->condition ?? '-' }}</x-ui.badge></td>
                        <td>{{ $f->acquisition_year ?? '-' }}</td>
                        <td style="color: var(--color-muted);">{{ $f->fund_source ?? '-' }}</td>
                        <td>
                            <div class="ui-table__actions">
                                <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" onclick="loadEditFacility({{ $f->id }})">Edit</button>
                                <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" style="color: var(--color-danger);" onclick="confirmDelete('{{ route('admin.facilities.destroy', $f) }}', '{{ $f->name }}')">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('components.dashboard.admin.pagination-controls', [
        'items' => $facilities,
        'search' => $search,
        'status' => $status,
        'per_page' => $per_page ?? 10,
    ])
@endif

<x-ui.modal name="facility-modal" title="Barang" maxWidth="560px">
    <div id="facility-modal-content">
        <p class="ui-stat-card__hint">Memuat...</p>
    </div>
</x-ui.modal>

<script>
    function loadCreateFacility() {
        window.loadFormIntoModal('{{ route('admin.facilities.create') }}', 'facility-modal-content', 'facility-modal');
    }
    function loadEditFacility(id) {
        window.loadFormIntoModal(`/admin/facilities/${id}/edit`, 'facility-modal-content', 'facility-modal');
    }
    function confirmDelete(url, label) {
        if (!confirm(`Hapus "${label}"?`)) return;
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch(url, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token }})
            .then(r => r.json()).then(() => location.reload())
            .catch(() => alert('Gagal menghapus.'));
    }
</script>

@include('components.dashboard.admin.admin-scripts')

@endsection
