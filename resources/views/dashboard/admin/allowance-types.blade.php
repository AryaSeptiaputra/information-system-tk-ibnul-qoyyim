@extends('layouts.dashboard')

@section('title', 'Jenis Tunjangan - TK Ibnul Qoyyim')
@section('page_title', 'Jenis Tunjangan')

@section('content')

<x-ui.page-header title="Jenis Tunjangan">
    <x-slot:action>
        <a href="{{ route('admin.positions.index', ['tab' => 'tunjangan']) }}" class="ui-btn ui-btn--ghost">
            ← Kembali ke Posisi & Tunjangan
        </a>
        <button type="button" class="ui-btn ui-btn--primary" onclick="loadCreateType()" style="margin-left: var(--ui-space-sm);">
            + Tambah Jenis
        </button>
    </x-slot:action>
</x-ui.page-header>

@if(session('success'))
    <x-ui.toast variant="success">{{ session('success') }}</x-ui.toast>
@endif

<div class="ui-toolbar">
    <form method="GET" action="{{ route('admin.allowance-types.index') }}" class="ui-toolbar__search">
        <input type="hidden" name="status" value="{{ $status ?? 'all' }}">
        <input type="search" name="search" value="{{ $search }}" class="ui-input" placeholder="Cari jenis tunjangan...">
    </form>
    <div class="ui-toolbar__filters">
        <form method="GET" action="{{ route('admin.allowance-types.index') }}">
            <input type="hidden" name="search" value="{{ $search }}">
            <select name="status" class="ui-select" onchange="this.form.submit()">
                <option value="all" @selected(($status ?? 'all') === 'all')>Semua Status</option>
                <option value="active" @selected(($status ?? '') === 'active')>Aktif</option>
                <option value="inactive" @selected(($status ?? '') === 'inactive')>Nonaktif</option>
            </select>
        </form>
    </div>
</div>

@if(($types?->isEmpty()) ?? true)
    <x-ui.empty-state icon="🎁" message="Belum ada jenis tunjangan." />
@else
    <div class="ui-table-wrapper">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Nama Jenis</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($types as $at)
                    <tr>
                        <td><strong>{{ $at->name }}</strong></td>
                        <td style="color: var(--color-muted);">{{ \Illuminate\Support\Str::limit($at->description ?? '-', 60) }}</td>
                        <td>
                            @if($at->is_active)
                                <x-ui.badge variant="success">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                            @endif
                        </td>
                        <td>
                            <div class="ui-table__actions">
                                <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" onclick="loadEditType({{ $at->id_allowance_type }})">Edit</button>
                                <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" style="color: var(--color-danger);" onclick="confirmDeleteType('{{ route('admin.allowance-types.destroy', $at) }}', '{{ addslashes($at->name) }}')">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('components.dashboard.admin.pagination-controls', [
        'items' => $types,
        'search' => $search,
        'status' => $status ?? 'all',
        'per_page' => $per_page ?? 10,
    ])
@endif

<x-ui.modal name="at-modal" title="Jenis Tunjangan" maxWidth="480px">
    <div id="at-modal-content">
        <p class="ui-stat-card__hint">Memuat...</p>
    </div>
</x-ui.modal>

<script>
    function loadCreateType() {
        window.loadFormIntoModal('{{ route('admin.allowance-types.create') }}', 'at-modal-content', 'at-modal');
    }
    function loadEditType(id) {
        window.loadFormIntoModal(`/admin/allowance-types/${id}/edit`, 'at-modal-content', 'at-modal');
    }
    function confirmDeleteType(url, label) {
        if (!confirm(`Hapus jenis tunjangan "${label}"?`)) return;
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch(url, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token }})
            .then(r => r.json()).then(() => location.reload())
            .catch(() => alert('Gagal menghapus.'));
    }
</script>

@endsection
