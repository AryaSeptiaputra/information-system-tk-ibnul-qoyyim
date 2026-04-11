@extends('layouts.dashboard')

@section('title', 'Manajemen Guru - TK Ibnul Qoyyim')
@section('page_title', 'Manajemen Guru')

@section('content')

<div class="admin-teachers-page">
    <!-- Section Header -->
    <div class="admin-section-header">
        <p class="admin-section-subtitle">Kelola data guru dan pendidik</p>
    </div>

    <!-- Search, Filter & Actions -->
    @include('components.dashboard.admin.section-header', [
        'type' => 'teacher',
        'search' => $search ?? '',
        'status' => $status ?? 'all',
        'exportUrl' => route('admin.teachers.export', request()->query()),
        'resetUrl' => route('admin.teachers.index'),
    ])

    <!-- Management Table -->
    @include('components.dashboard.admin.management-table', [
        'type' => 'teacher',
        'items' => $teachers ?? collect(),
    ])

    <!-- Pagination -->
    @if($teachers && $teachers->total() > 0)
        @include('components.dashboard.admin.pagination-controls', [
            'items' => $teachers,
            'search' => $search ?? '',
            'status' => $status ?? 'all',
            'per_page' => $per_page ?? 10,
        ])
    @endif
</div>

@include('components.dashboard.admin.admin-scripts')

@endsection
