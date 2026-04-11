@extends('layouts.dashboard')

@section('title', 'Manajemen Tagihan Murid - TK Ibnul Qoyyim')
@section('page_title', 'Manajemen Tagihan Murid')

@section('content')

<div class="admin-users-page">
    <div class="admin-section-header">
        <p class="admin-section-subtitle">Kelola tagihan pembayaran murid (unik per murid + payment + periode)</p>
    </div>

    @include('components.dashboard.admin.section-header', [
        'type' => 'student-payment',
        'search' => $search ?? '',
        'status' => $status ?? 'all',
        'id_payment' => $id_payment ?? 'all',
        'payments' => $payments ?? collect(),
        'exportUrl' => route('admin.student-payments.export', request()->query()),
        'resetUrl' => route('admin.student-payments.index'),
    ])

    @include('components.dashboard.admin.management-table', [
        'type' => 'student-payment',
        'items' => $studentPayments ?? collect(),
    ])

    @if($studentPayments && $studentPayments->total() > 0)
        @include('components.dashboard.admin.pagination-controls', [
            'items' => $studentPayments,
            'search' => $search ?? '',
            'status' => $status ?? 'all',
            'id_payment' => $id_payment ?? 'all',
            'per_page' => $per_page ?? 10,
        ])
    @endif
</div>

@include('components.dashboard.admin.admin-scripts')

@endsection
