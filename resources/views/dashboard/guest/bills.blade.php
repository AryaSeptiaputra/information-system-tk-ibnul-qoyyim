@extends('layouts.dashboard')

@section('title', 'Tagihan')
@section('page_title', 'Tagihan')

@section('content')
    @if (session('success'))
        <div class="card" style="margin-bottom: 16px; border-left: 5px solid var(--green);">
            <strong style="color: var(--green-dark);">{{ session('success') }}</strong>
        </div>
    @endif

    @if (session('error'))
        <div class="card" style="margin-bottom: 16px; border-left: 5px solid #b91c1c;">
            <strong style="color: #b91c1c;">{{ session('error') }}</strong>
        </div>
    @endif

    @include('components.dashboard.guest-bills', [
        'studentPayments' => $studentPayments ?? collect(),
        'studentBillGroups' => $studentBillGroups ?? collect(),
        'paymentMethods' => $paymentMethods ?? collect(),
        'paymentSettings' => $paymentSettings ?? null,
    ])
@endsection
