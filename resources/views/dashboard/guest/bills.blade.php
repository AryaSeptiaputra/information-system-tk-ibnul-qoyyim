@extends('layouts.dashboard')

@section('title', 'Tagihan & Bayar - TK Ibnul Qoyyim')
@section('page_title', 'Tagihan & Bayar')

@php
    $rp = static fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $bs = $billSummary ?? [];
@endphp

@section('content')

<x-ui.page-header title="Tagihan & Bayar" />

@if (session('success'))
    <x-ui.toast variant="success">{{ session('success') }}</x-ui.toast>
@endif

@if (session('error'))
    <x-ui.toast variant="danger">{{ session('error') }}</x-ui.toast>
@endif

<div class="ui-stat-card-grid" style="margin-bottom: var(--ui-space-lg);">
    <x-ui.stat-card
        label="Total Tagihan"
        :value="(int)($bs['total_bills'] ?? 0)"
        hint="semua tagihan"
    />
    <x-ui.stat-card
        label="Belum Bayar"
        :value="(int)($bs['pending_bills'] ?? 0) + (int)($bs['failed_bills'] ?? 0)"
        :hint="$rp($bs['outstanding_amount'] ?? 0) . ' outstanding'"
    />
    <x-ui.stat-card
        label="Menunggu Verifikasi"
        :value="(int)($bs['waiting_verification'] ?? 0)"
        hint="bukti di-review admin"
    />
</div>

{{-- Content tagihan: reuse partial existing yang sudah handle modal bayar, installments, dll --}}
@include('components.dashboard.guest-bills', [
    'studentPayments' => $studentPayments ?? collect(),
    'studentBillGroups' => $studentBillGroups ?? collect(),
    'paymentMethods' => $paymentMethods ?? collect(),
    'paymentSettings' => $paymentSettings ?? null,
])

@endsection
