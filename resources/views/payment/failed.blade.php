@extends('layouts.landing')

@section('content')
<div class="payment-result-container error">
    <div class="payment-result-wrapper">
        <div class="result-icon error-icon">
            <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                <circle cx="32" cy="32" r="30" stroke="currentColor" stroke-width="2"/>
                <path d="M24 24L40 40M40 24L24 40" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
            </svg>
        </div>

        <h1>Pembayaran Gagal</h1>
        <p class="result-message">Terjadi kesalahan saat memproses pembayaran Anda. Silakan coba lagi.</p>

        <div class="result-details">
            <div class="detail-row">
                <span>Nama Siswa:</span>
                <strong>{{ $student->name }}</strong>
            </div>
            <div class="detail-row">
                <span>Total Biaya:</span>
                <strong>Rp {{ number_format($payment->final_amount, 0, ',', '.') }}</strong>
            </div>
            <div class="detail-row">
                <span>Status:</span>
                <strong>{{ ucfirst($payment->status) }}</strong>
            </div>
        </div>

        <div class="error-message">
            <strong>Penyebab Kemungkinan Kegagalan:</strong>
            <ul>
                <li>Koneksi internet terputus</li>
                <li>Nominal pembayaran tidak sesuai</li>
                <li>Kode unik tidak dimasukkan dengan benar</li>
                <li>Waktu transaksi sudah expired</li>
            </ul>
        </div>

        <div class="action-buttons">
            <a href="{{ route('payment.create', $student->id_student) }}" class="btn btn-retry">
                🔄 Coba Lagi
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                ← Kembali ke Dashboard
            </a>
        </div>

        <p class="support-text">
            <strong>Butuh Bantuan?</strong><br>
            Hubungi pihak sekolah di <strong>+62-812-3456-7890</strong> atau datang langsung ke sekolah.
        </p>
    </div>
</div>

<style>
    .payment-result-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
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
        color: #f44336;
        display: flex;
        justify-content: center;
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
        margin-bottom: 20px;
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

    .error-message {
        background: #fff3e0;
        border: 1px solid #ffe0b2;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        text-align: left;
        color: #e65100;
    }

    .error-message strong {
        display: block;
        margin-bottom: 10px;
    }

    .error-message ul {
        margin: 0;
        padding-left: 20px;
    }

    .error-message li {
        margin: 5px 0;
        font-size: 13px;
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

    .btn-retry {
        background: #f44336;
        color: white;
    }

    .btn-retry:hover {
        background: #d32f2f;
    }

    .btn-secondary {
        background: #f0f0f0;
        color: #333;
    }

    .btn-secondary:hover {
        background: #e8e8e8;
    }

    .support-text {
        font-size: 12px;
        color: #666;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .support-text strong {
        display: block;
        margin-bottom: 5px;
    }

    @media (max-width: 600px) {
        .payment-result-wrapper {
            padding: 30px 20px;
        }

        .payment-result-wrapper h1 {
            font-size: 22px;
        }

        .result-details,
        .error-message {
            padding: 15px;
        }

        .detail-row {
            font-size: 13px;
        }
    }
</style>
@endsection
