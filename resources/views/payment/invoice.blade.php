@extends('layouts.dashboard')

@section('title', 'Invoice Pembayaran')
@section('page_title', 'Invoice Pembayaran')

@section('content')
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <h2 style="margin-bottom: 8px;">Invoice</h2>
        <div style="margin-bottom: 16px; color: var(--gray);">ID: {{ $payment->getKey() }}</div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <h3>Informasi Siswa</h3>
                <div>Nama: {{ $student->name ?? '-' }}</div>
                <div>Kelompok: {{ $student->group ?? '-' }}</div>
            </div>
            <div>
                <h3>Informasi Pembayaran</h3>
                <div>Metode: {{ strtoupper(str_replace('_', ' ', $payment->payment_method ?? '-')) }}</div>
                <div>Status: {{ strtoupper($payment->status ?? '-') }}</div>
                <div>Tanggal bayar: {{ $payment->payment_date ?? '-' }}</div>
            </div>
        </div>

        <hr style="margin: 20px 0;" />

        <h3>Rincian</h3>
        @php
            $rows = $items ?? [];
        @endphp
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 8px 0; border-bottom: 1px solid var(--green-light);">Item</th>
                    <th style="text-align: right; padding: 8px 0; border-bottom: 1px solid var(--green-light);">Harga</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td style="padding: 10px 0; border-bottom: 1px solid #eee;">{{ $row->description ?? ($row->item_code ?? 'Item') }}</td>
                        <td style="padding: 10px 0; border-bottom: 1px solid #eee; text-align: right;">Rp {{ number_format((float)($row->amount ?? 0), 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="padding: 10px 0;">Belum ada item.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 16px; text-align: right; font-weight: 800;">
            Total: Rp {{ number_format((float)($payment->total_amount ?? 0), 0, ',', '.') }}
        </div>

        <div style="margin-top: 20px;">
            <a href="{{ route('dashboard') }}" class="btn-primary">Kembali ke Dashboard</a>
        </div>
    </div>
@endsection
