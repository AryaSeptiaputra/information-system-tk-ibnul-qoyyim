@extends('layouts.dashboard')

@section('title', 'Penugasan Posisi Guru - TK Ibnul Qoyyim')
@section('page_title', 'Penugasan Posisi Guru')

@section('content')

<div class="admin-teacher-positions-page">
    <div class="admin-section-header">
        <p class="admin-section-subtitle">Atur posisi guru per periode.</p>
    </div>

    @include('components.dashboard.admin.section-header', [
        'type' => 'teacher-position',
        'search' => $search ?? '',
        'exportUrl' => route('admin.teacher-positions.export', request()->query()),
        'resetUrl' => route('admin.teacher-positions.index'),
    ])

    @include('components.dashboard.admin.management-table', [
        'type' => 'teacher-position',
        'items' => $assignments ?? collect(),
        'showDelete' => true,
    ])

    @if($assignments)
        @include('components.dashboard.admin.pagination-controls', [
            'items' => $assignments,
            'search' => $search ?? '',
            'per_page' => $per_page ?? 10,
        ])
    @endif
</div>

@include('components.dashboard.admin.admin-scripts')

@endsection
