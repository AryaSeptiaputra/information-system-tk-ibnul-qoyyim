@extends('layouts.landing')

@section('content')
<div class="payment-result-container success">
    <div class="payment-result-wrapper">
        <div class="result-icon success-icon">
            <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                <circle cx="32" cy="32" r="30" stroke="currentColor" stroke-width="2"/>
                <path d="M20 32L28 40L44 24" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <h1>Pembayaran Berhasil!</h1>
        <p class="result-message">Terima kasih atas pembayaran Anda. Pendaftaran siswa telah dikonfirmasi.</p>

        <div class="result-details">
            <div class="detail-row">
                <span>Nama Siswa:</span>
                <strong>{{ $student->name }}</strong>
            </div>
            <div class="detail-row">
                <span>Metode Pembayaran:</span>
                <strong>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</strong>
            </div>
            <div class="detail-row">
                <span>Total Pembayaran:</span>
                <strong>Rp {{ number_format($payment->final_amount, 0, ',', '.') }}</strong>
            </div>
            @if($payment->is_late)
                <div class="detail-row warning">
                    <span>⚠️ Status Pembayaran:</span>
                    <strong>Terlambat</strong>
                </div>
            @endif
            <div class="detail-row">
                <span>Tanggal Pembayaran:</span>
                <strong>{{ $payment->paid_at ? $payment->paid_at->format('d M Y H:i') : 'Menunggu Konfirmasi' }}</strong>
            </div>
        </div>

        <div class="action-buttons">
            <a href="{{ route('payment.invoice', $payment->id_student_payment) }}" class="btn btn-primary">
                📄 Download Invoice
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                ← Kembali ke Dashboard
            </a>
        </div>

        <p class="footer-text">Kami telah mengirimkan konfirmasi pembayaran ke email Anda. Jika ada pertanyaan, silakan hubungi pihak sekolah.</p>
    </div>
</div>

<style>
    .payment-result-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
    }

    .payment-result-container.success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    }

    .payment-result-container.error {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    }

    .payment-result-wrapper {
        background: white;
        border-radius: 12px;
        padding: 40px;
        max-width: 500px;
        width: 100%;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        text-align: center;
    }

    .result-icon {
        margin-bottom: 30px;
        color: #4caf50;
        display: flex;
        justify-content: center;
    }

    .result-icon.error-icon {
        color: #f44336;
    }

    .payment-result-wrapper h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 12px;
    }

    .result-message {
        font-size: 16px;
        color: #666;
        margin-bottom: 30px;
    }

    .result-details {
        background: #f9f9f9;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
        text-align: left;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
        font-size: 14px;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-row strong {
        color: #333;
        font-weight: 600;
    }

    .detail-row.warning {
        color: #ff9800;
        background: #fff3e0;
        padding: 10px;
        border-radius: 4px;
        margin-top: 10px;
    }

    .detail-row.warning strong {
        color: #e65100;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 20px;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #4caf50;
        color: white;
    }

    .btn-primary:hover {
        background: #45a049;
    }

    .btn-secondary {
        background: #f0f0f0;
        color: #333;
    }

    .btn-secondary:hover {
        background: #e8e8e8;
    }

    .footer-text {
        font-size: 12px;
        color: #999;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    @media (max-width: 600px) {
        .payment-result-wrapper {
            padding: 30px 20px;
        }

        .payment-result-wrapper h1 {
            font-size: 22px;
        }

        .result-details {
            padding: 15px;
        }

        .detail-row {
            font-size: 13px;
        }
    }
</style>
@endsection
