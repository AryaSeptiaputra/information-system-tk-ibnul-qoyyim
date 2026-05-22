@extends('layouts.dashboard')

@section('title', 'Posisi & Tunjangan - TK Ibnul Qoyyim')
@section('page_title', 'Posisi & Tunjangan')

@php
    $rp = static fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');

    $tabs = [
        ['key' => 'posisi', 'label' => 'Posisi', 'url' => route('admin.positions.index', ['tab' => 'posisi'])],
        ['key' => 'tunjangan', 'label' => 'Jenis Tunjangan', 'url' => route('admin.positions.index', ['tab' => 'tunjangan'])],
        ['key' => 'nominal', 'label' => 'Nominal & Penugasan', 'url' => route('admin.positions.index', ['tab' => 'nominal'])],
    ];
@endphp

@section('content')

<x-ui.page-header title="Posisi & Tunjangan">
    <x-slot:action>
        @if($tab === 'posisi')
            <button type="button" class="ui-btn ui-btn--primary" onclick="loadFormModal('position')">+ Tambah Posisi</button>
        @elseif($tab === 'tunjangan')
            <button type="button" class="ui-btn ui-btn--primary" onclick="loadFormModal('allowance-type')">+ Tambah Jenis</button>
        @endif
    </x-slot:action>
</x-ui.page-header>

@if(session('success'))
    <x-ui.toast variant="success">{{ session('success') }}</x-ui.toast>
@endif

<x-ui.tab-bar :tabs="$tabs" :active="$tab" />

{{-- ============ TAB POSISI ============ --}}
@if($tab === 'posisi')
    <div class="ui-toolbar">
        <form method="GET" action="{{ route('admin.positions.index') }}" class="ui-toolbar__search">
            <input type="hidden" name="tab" value="posisi">
            <input type="search" name="search" value="{{ $search }}" class="ui-input" placeholder="Cari posisi...">
        </form>
        <div class="ui-toolbar__filters">
            <form method="GET" action="{{ route('admin.positions.index') }}">
                <input type="hidden" name="tab" value="posisi">
                <input type="hidden" name="search" value="{{ $search }}">
                <select name="status" class="ui-select" onchange="this.form.submit()">
                    <option value="all" @selected($status === 'all')>Semua Status</option>
                    <option value="active" @selected($status === 'active')>Aktif</option>
                    <option value="inactive" @selected($status === 'inactive')>Nonaktif</option>
                </select>
            </form>
        </div>
    </div>

    @if($positions->isEmpty())
        <x-ui.empty-state message="Belum ada posisi." />
    @else
        <div class="ui-table-wrapper">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Nama Posisi</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th style="text-align: center;">Guru</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($positions as $p)
                        <tr>
                            <td><strong>{{ $p->name }}</strong></td>
                            <td style="color: var(--color-muted);">{{ \Illuminate\Support\Str::limit($p->description ?? '-', 60) }}</td>
                            <td>
                                @if($p->is_active)
                                    <x-ui.badge variant="success">Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                                @endif
                            </td>
                            <td style="text-align: center;">{{ (int) $p->teacher_positions_count }}</td>
                            <td>
                                <div class="ui-table__actions">
                                    <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" onclick="loadEditModal('position', {{ $p->id_position }})">Edit</button>
                                    <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" style="color: var(--color-danger);" onclick="confirmDelete('{{ route('admin.positions.destroy', $p) }}', '{{ $p->name }}')">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @include('components.dashboard.admin.pagination-controls', ['items' => $positions, 'tab' => 'posisi', 'search' => $search, 'status' => $status, 'per_page' => $per_page])
    @endif

{{-- ============ TAB JENIS TUNJANGAN ============ --}}
@elseif($tab === 'tunjangan')
    <div class="ui-toolbar">
        <form method="GET" action="{{ route('admin.positions.index') }}" class="ui-toolbar__search">
            <input type="hidden" name="tab" value="tunjangan">
            <input type="search" name="search" value="{{ $search }}" class="ui-input" placeholder="Cari jenis tunjangan...">
        </form>
    </div>

    @if($allowanceTypes->isEmpty())
        <x-ui.empty-state message="Belum ada jenis tunjangan." />
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
                    @foreach($allowanceTypes as $at)
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
                                    <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" onclick="loadEditModal('allowance-type', {{ $at->id_allowance_type }})">Edit</button>
                                    <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" style="color: var(--color-danger);" onclick="confirmDelete('{{ route('admin.allowance-types.destroy', $at) }}', '{{ $at->name }}')">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @include('components.dashboard.admin.pagination-controls', ['items' => $allowanceTypes, 'tab' => 'tunjangan', 'search' => $search, 'per_page' => $per_page])
    @endif

{{-- ============ TAB NOMINAL & PENUGASAN ============ --}}
@else
    <p class="ui-stat-card__hint" style="margin-bottom: var(--ui-space-md);">
        Daftar posisi aktif beserta nominal tunjangan & guru yang ditugaskan saat ini.
    </p>

    @if(($positionsWithRelations?->isEmpty()) ?? true)
        <x-ui.empty-state message="Belum ada posisi aktif. Tambahkan di tab Posisi." />
    @else
        @foreach($positionsWithRelations as $pos)
            <div class="ui-section">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--ui-space-md); flex-wrap: wrap; gap: var(--ui-space-sm);">
                    <h3 class="ui-section__title" style="margin: 0;">{{ $pos->name }}</h3>
                    <div style="display: flex; gap: var(--ui-space-sm);">
                        <a href="{{ route('admin.position-allowances.index', ['id_position' => $pos->id_position]) }}" class="ui-btn ui-btn--secondary ui-btn--sm">+ Tunjangan</a>
                        <a href="{{ route('admin.teacher-positions.index', ['id_position' => $pos->id_position]) }}" class="ui-btn ui-btn--secondary ui-btn--sm">+ Assign Guru</a>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--ui-space-lg);">
                    <div>
                        <div class="ui-stat-card__label" style="margin-bottom: var(--ui-space-sm);">Tunjangan Aktif</div>
                        @if($pos->allowances->isEmpty())
                            <p class="ui-stat-card__hint">Belum ada tunjangan.</p>
                        @else
                            <table class="ui-table" style="font-size: var(--ui-font-sm);">
                                <tbody>
                                    @foreach($pos->allowances as $a)
                                        <tr>
                                            <td>{{ $a->allowanceType?->name ?? '-' }}</td>
                                            <td style="text-align: right;"><strong>{{ $rp($a->amount) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    <div>
                        <div class="ui-stat-card__label" style="margin-bottom: var(--ui-space-sm);">Guru Ditugaskan</div>
                        @if($pos->teacherPositions->isEmpty())
                            <p class="ui-stat-card__hint">Belum ada guru ditugaskan.</p>
                        @else
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                @foreach($pos->teacherPositions as $tp)
                                    <li style="padding: var(--ui-space-xs) 0; border-bottom: 1px solid var(--color-border); font-size: var(--ui-font-sm);">
                                        {{ $tp->teacher?->name ?? '-' }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endif

{{-- Modal form (load via AJAX) --}}
<x-ui.modal name="form-modal" title="Form" maxWidth="560px">
    <div id="form-modal-content">
        <p class="ui-stat-card__hint">Memuat formulir...</p>
    </div>
</x-ui.modal>

<script>
    const URL_CREATE = {
        'position': '{{ route('admin.positions.create') }}',
        'allowance-type': '{{ route('admin.allowance-types.create') }}',
    };

    function loadFormModal(type) {
        const url = URL_CREATE[type];
        if (!url) return;
        window.loadFormIntoModal(url, 'form-modal-content', 'form-modal');
    }

    function loadEditModal(type, id) {
        const editUrls = {
            'position': `/admin/positions/${id}/edit`,
            'allowance-type': `/admin/allowance-types/${id}/edit`,
        };
        const url = editUrls[type];
        if (!url) return;
        window.loadFormIntoModal(url, 'form-modal-content', 'form-modal');
    }

    function confirmDelete(url, label) {
        if (!confirm(`Hapus "${label}"? Aksi ini tidak bisa dibatalkan.`)) return;
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch(url, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
        })
            .then(r => r.json())
            .then(() => location.reload())
            .catch(() => alert('Gagal menghapus.'));
    }
</script>

@include('components.dashboard.admin.admin-scripts')

@endsection
