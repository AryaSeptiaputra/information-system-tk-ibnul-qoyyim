@extends('layouts.dashboard')

@section('title', 'Manajemen Pengguna - TK Ibnul Qoyyim')
@section('page_title', 'Manajemen Pengguna')

@section('content')

<div class="admin-users-page">
    <!-- Section Header -->
    <div class="admin-section-header">
        <p class="admin-section-subtitle">Kelola data pengguna sistem</p>
    </div>

    <!-- Search, Filter & Actions -->
    @include('components.dashboard.admin.section-header', [
        'type' => 'user',
        'search' => $search ?? '',
        'role' => $role ?? 'all',
        'status' => $status ?? 'all',
        'exportUrl' => route('admin.users.export', request()->query()),
        'resetUrl' => route('admin.users.index'),
    ])

    <!-- Management Table -->
    @include('components.dashboard.admin.management-table', [
        'type' => 'user',
        'items' => $users ?? collect(),
    ])

    <!-- Pagination -->
    @if($users && $users->total() > 0)
        @include('components.dashboard.admin.pagination-controls', [
            'items' => $users,
            'search' => $search ?? '',
            'role' => $role ?? 'all',
            'status' => $status ?? 'all',
            'per_page' => $per_page ?? 10,
        ])
    @endif
</div>

@include('components.dashboard.admin.admin-scripts')

@endsection
