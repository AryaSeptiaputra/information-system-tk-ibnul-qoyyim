@extends('layouts.dashboard')

@section('title', 'Data Kelas - TK Ibnul Qoyyim')
@section('page_title', 'Data Kelas')

@section('content')

@php
    $canManageClass = in_array(auth()->user()?->role, ['superadmin', 'administration'], true);
@endphp

<x-ui.page-header title="Data Kelas">
    <x-slot:action>
        <a href="{{ route('admin.classes.export', request()->query()) }}"
           class="ui-btn ui-btn--ghost ui-btn--sm" title="Download CSV">📥 Export</a>
        @if($canManageClass)
            <button type="button" class="ui-btn ui-btn--primary"
                    data-modal-open="add-class-modal">
                + Tambah Kelas
            </button>
        @endif
    </x-slot:action>
</x-ui.page-header>

@if(session('success'))
    <x-ui.toast variant="success">{{ session('success') }}</x-ui.toast>
@endif
@if(session('error'))
    <x-ui.toast variant="danger">{{ session('error') }}</x-ui.toast>
@endif

<div class="ui-toolbar">
    <form method="GET" action="{{ route('admin.classes.index') }}" class="ui-toolbar__search">
        <input type="hidden" name="school_year" value="{{ $school_year }}">
        <input type="search" name="search" value="{{ $search }}"
               class="ui-input" placeholder="Cari nama kelas / tahun ajaran...">
    </form>

    <div class="ui-toolbar__filters">
        <form method="GET" action="{{ route('admin.classes.index') }}">
            <input type="hidden" name="search" value="{{ $search }}">

            <select name="school_year" class="ui-select" onchange="this.form.submit()">
                <option value="all" @selected($school_year === 'all')>Semua Tahun Ajaran</option>
                @foreach($schoolYears as $yr)
                    <option value="{{ $yr }}" @selected((string) $school_year === (string) $yr)>
                        {{ $yr }}
                    </option>
                @endforeach
            </select>

            <a href="{{ route('admin.classes.index') }}"
               class="ui-btn ui-btn--ghost ui-btn--sm">Reset</a>
        </form>
    </div>
</div>

@if($classes->isEmpty())
    <x-ui.empty-state icon="🏫" message="Belum ada data kelas." />
@else
    <div class="ui-table-wrapper">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Nama Kelas</th>
                    <th>Tahun Ajaran</th>
                    <th style="text-align: center;">Murid</th>
                    <th style="text-align: center;">Kapasitas</th>
                    <th>Dibuat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($classes as $c)
                    @php
                        $studentsCount = (int) ($c->students_count ?? 0);
                        $maxStudents = (int) ($c->max_students ?? 0);
                        $isFull = $maxStudents > 0 && $studentsCount >= $maxStudents;
                        $isOver = $maxStudents > 0 && $studentsCount > $maxStudents;
                    @endphp
                    <tr>
                        <td><strong>{{ $c->class_name ?? '-' }}</strong></td>
                        <td>{{ $c->school_year ?? '-' }}</td>
                        <td style="text-align: center;">
                            @if($isOver)
                                <x-ui.badge variant="danger">{{ $studentsCount }}</x-ui.badge>
                            @elseif($isFull)
                                <x-ui.badge variant="warning">{{ $studentsCount }} (Penuh)</x-ui.badge>
                            @else
                                {{ $studentsCount }}
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $maxStudents > 0 ? $maxStudents : '-' }}</td>
                        <td style="color: var(--color-muted);">{{ $c->created_at?->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            <div class="ui-table__actions">
                                <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm"
                                        data-modal-open="view-class-modal"
                                        data-item-id="{{ $c->id_class }}">Lihat</button>
                                @if($canManageClass)
                                    <button type="button" class="ui-btn ui-btn--ghost ui-btn--sm"
                                            data-modal-open="edit-class-modal"
                                            data-item-id="{{ $c->id_class }}">Edit</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('components.dashboard.admin.pagination-controls', [
        'items'       => $classes,
        'search'      => $search,
        'school_year' => $school_year,
        'per_page'    => $per_page,
    ])
@endif

@include('components.dashboard.admin.admin-scripts')

@endsection
