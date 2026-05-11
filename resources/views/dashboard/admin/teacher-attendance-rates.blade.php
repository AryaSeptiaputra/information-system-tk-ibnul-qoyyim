@extends('layouts.dashboard')

@section('title', 'Tarif Kehadiran Guru - TK Ibnul Qoyyim')
@section('page_title', 'Tarif Kehadiran Guru')

@section('content')

<div class="admin-teacher-attendance-rates-page">
    <div class="admin-section-header">
        <p class="admin-section-subtitle">Kelola tarif per hadir untuk guru.</p>
    </div>

    @include('components.dashboard.admin.section-header', [
        'type' => 'teacher-attendance-rate',
        'search' => $search ?? '',
        'exportUrl' => route('admin.teacher-attendance-rates.export', request()->query()),
        'resetUrl' => route('admin.teacher-attendance-rates.index'),
    ])

    @include('components.dashboard.admin.management-table', [
        'type' => 'teacher-attendance-rate',
        'items' => $rates ?? collect(),
        'showDelete' => true,
    ])

    @if($rates)
        @include('components.dashboard.admin.pagination-controls', [
            'items' => $rates,
            'search' => $search ?? '',
            'per_page' => $per_page ?? 10,
        ])
    @endif
</div>

@include('components.dashboard.admin.admin-scripts')

@endsection
