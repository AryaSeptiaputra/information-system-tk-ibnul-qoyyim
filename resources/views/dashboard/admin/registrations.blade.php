@extends('layouts.dashboard')

@section('title', 'Manajemen Pendaftaran - TK Ibnul Qoyyim')
@section('page_title', 'Manajemen Pendaftaran')

@section('content')

<div class="admin-registrations-page">
    <div class="admin-section-header">
        <p class="admin-section-subtitle">Kelola data pendaftaran calon murid</p>
    </div>

    @include('components.dashboard.admin.section-header', [
        'type' => 'registration',
        'search' => $search ?? '',
        'status' => $status ?? 'all',
        'group' => $group ?? 'all',
        'exportUrl' => route('admin.registrations.export', request()->query()),
        'resetUrl' => route('admin.registrations.index'),
    ])

    @include('components.dashboard.admin.management-table', [
        'type' => 'registration',
        'items' => $registrations ?? collect(),
        'showDelete' => false,
    ])

    @if($registrations && $registrations->total() > 0)
        @include('components.dashboard.admin.pagination-controls', [
            'items' => $registrations,
            'search' => $search ?? '',
            'status' => $status ?? 'all',
            'group' => $group ?? 'all',
            'per_page' => $per_page ?? 10,
        ])
    @endif
</div>

@include('components.dashboard.admin.admin-scripts')

@endsection
