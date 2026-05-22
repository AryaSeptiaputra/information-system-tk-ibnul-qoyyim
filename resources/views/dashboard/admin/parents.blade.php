@extends('layouts.dashboard')

@section('title', 'Data Orangtua-Murid - TK Ibnul Qoyyim')
@section('page_title', 'Data Orangtua-Murid')

@php
    $tabs = [
        ['key' => 'orangtua', 'label' => 'Orangtua', 'url' => route('admin.parents.index', ['tab' => 'orangtua'])],
        ['key' => 'murid', 'label' => 'Murid', 'url' => route('admin.parents.index', ['tab' => 'murid'])],
    ];
@endphp

@section('content')

<x-ui.page-header title="Data Orangtua-Murid">
    <x-slot:action>
        @if($tab === 'orangtua')
            <button type="button" class="ui-btn ui-btn--primary" onclick="loadAjaxModal('{{ route('admin.parents.create') }}')">+ Tambah Orangtua</button>
        @else
            <button type="button" class="ui-btn ui-btn--primary" onclick="loadAjaxModal('{{ route('admin.students.create') }}')">+ Tambah Murid</button>
        @endif
    </x-slot:action>
</x-ui.page-header>

@if(session('success'))
    <x-ui.toast variant="success">{{ session('success') }}</x-ui.toast>
@endif

<x-ui.tab-bar :tabs="$tabs" :active="$tab" />

<div class="ui-toolbar">
    <form method="GET" action="{{ route('admin.parents.index') }}" class="ui-toolbar__search">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input type="search" name="search" value="{{ $search }}" class="ui-input" placeholder="{{ $tab === 'orangtua' ? 'Cari orangtua...' : 'Cari murid...' }}">
    </form>
</div>

{{-- ============ TAB ORANGTUA ============ --}}
@if($tab === 'orangtua')
    @if($parents->isEmpty())
        <x-ui.empty-state message="Belum ada data orangtua." />
    @else
        <div class="ui-table-wrapper">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Nama Ayah</th>
                        <th>Nama Ibu</th>
                        <th>Anak</th>
                        <th>Kontak</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parents as $p)
                        @php
                            $children = $p->students->pluck('name')->filter()->implode(', ');
                            $contact = $p->father_phone_num ?: $p->mother_phone_num ?: null;
                        @endphp
                        <tr>
                            <td><strong>{{ $p->father_name ?? '-' }}</strong></td>
                            <td>{{ $p->mother_name ?? '-' }}</td>
                            <td style="color: var(--color-muted);">{{ $children !== '' ? $children : '-' }}</td>
                            <td>{{ $contact ?? '-' }}</td>
                            <td>
                                <div class="ui-table__actions">
                                    <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" onclick="loadAjaxModal('{{ route('admin.parents.show', $p) }}')">Lihat</button>
                                    <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" onclick="loadAjaxModal('/admin/parents/{{ $p->id_parents }}/edit')">Edit</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @include('components.dashboard.admin.pagination-controls', ['items' => $parents, 'tab' => $tab, 'search' => $search, 'per_page' => $per_page])
    @endif

{{-- ============ TAB MURID ============ --}}
@else
    @if($students->isEmpty())
        <x-ui.empty-state message="Belum ada data murid." />
    @else
        <div class="ui-table-wrapper">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Nama Murid</th>
                        <th>Gender</th>
                        <th>Grup</th>
                        <th>Orangtua</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $s)
                        @php
                            $parentName = $s->parent
                                ? trim(($s->parent->father_name ?? '') . (($s->parent->father_name && $s->parent->mother_name) ? ' / ' : '') . ($s->parent->mother_name ?? ''))
                                : '-';
                            $statusBadge = match ($s->status ?? '') {
                                'aktif' => 'success',
                                'rejected' => 'danger',
                                default => 'neutral',
                            };
                        @endphp
                        <tr>
                            <td><strong>{{ $s->name ?? '-' }}</strong></td>
                            <td>{{ $s->gender ?? '-' }}</td>
                            <td>{{ $s->group ?? '-' }}</td>
                            <td style="color: var(--color-muted);">{{ $parentName ?: '-' }}</td>
                            <td><x-ui.badge :variant="$statusBadge">{{ ucfirst($s->status ?? '-') }}</x-ui.badge></td>
                            <td>
                                <div class="ui-table__actions">
                                    <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" onclick="loadAjaxModal('{{ route('admin.students.show', $s) }}')">Lihat</button>
                                    <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm" onclick="loadAjaxModal('/admin/students/{{ $s->id_student }}/edit')">Edit</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @include('components.dashboard.admin.pagination-controls', ['items' => $students, 'tab' => $tab, 'search' => $search, 'per_page' => $per_page])
    @endif
@endif

<x-ui.modal name="ortu-murid-modal" title="" maxWidth="720px">
    <div id="ortu-murid-modal-content">
        <p class="ui-stat-card__hint">Memuat...</p>
    </div>
</x-ui.modal>

<script>
    function loadAjaxModal(url) {
        // Heuristic: URL berakhiran /edit atau /create → form (perlu submit handler); selain itu → detail
        const isForm = /\/(edit|create)(\?|$)/.test(url);
        if (isForm) {
            window.loadFormIntoModal(url, 'ortu-murid-modal-content', 'ortu-murid-modal');
        } else {
            window.loadDetailIntoModal(url, 'ortu-murid-modal-content', 'ortu-murid-modal');
        }
    }
</script>

@include('components.dashboard.admin.admin-scripts')

@endsection
