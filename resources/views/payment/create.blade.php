@extends('layouts.landing')

@section('content')
<div class="payment-container">
    <div class="payment-wrapper">
        <h1 class="page-title">Formulir Pembayaran Pendaftaran</h1>

        @if(isset($payment) && $payment->isPaid())
            <div class="alert alert-success">
                <strong>✓ Pembayaran Sudah Dikonfirmasi</strong>
                <p>Berterima kasih! Pendaftaran Anda telah selesai.</p>
                <a href="{{ route('payment.invoice', $payment->id_student_payment) }}" class="btn btn-primary">Lihat Invoice</a>
            </div>
        @else
            <!-- Payment Info -->
            <div class="payment-info-card">
                <h2>Data Siswa</h2>
                <p><strong>Nama:</strong> {{ $student->name }}</p>
                <p><strong>Kelompok:</strong> {{ $student->group }}</p>
                <p><strong>Kelahiran:</strong> {{ $student->birth_place }}, {{ $student->birth_date->format('d M Y') }}</p>
            </div>

            <!-- Fee Breakdown -->
            <div class="payment-breakdown-card">
                <h2>Rincian Biaya</h2>
                <table class="breakdown-table">
                    <thead>
                        <tr>
                            <th>Item Biaya</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($breakdown['items'] as $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                                <td><strong>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="breakdown-summary">
                    <div class="summary-row">
                        <span>Total Biaya:</span>
                        <strong>Rp {{ number_format($breakdown['total_amount'], 0, ',', '.') }}</strong>
                    </div>
                    @if($breakdown['discount_amount'] > 0)
                        <div class="summary-row discount">
                            <span>Diskon:</span>
                            <strong>-Rp {{ number_format($breakdown['discount_amount'], 0, ',', '.') }}</strong>
                        </div>
                    @endif
                    <div class="summary-row total">
                        <span>Total Pembayaran:</span>
                        <strong>Rp {{ number_format($breakdown['final_amount'], 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="payment-methods-card">
                <h2>Pilih Metode Pembayaran</h2>
                <form action="{{ route('payment.store') }}" method="POST" enctype="multipart/form-data" id="paymentForm">
                    @csrf
                    <input type="hidden" name="id_student" value="{{ $student->id_student }}">

                    <div class="payment-methods">
                        @foreach($paymentMethods as $index => $method)
                            <div class="payment-method-option">
                                <input 
                                    type="radio" 
                                    id="method_{{ $method['code'] }}" 
                                    name="payment_method" 
                                    value="{{ $method['code'] }}"
                                    {{ $index === 0 ? 'checked' : '' }}
                                    onchange="updateMethodInfo(this)"
                                >
                                <label for="method_{{ $method['code'] }}" class="method-label">
                                    <span class="method-name">{{ $method['name'] }}</span>
                                    <span class="method-desc">{{ $method['description'] }}</span>
                                </label>
                                <div class="method-info" id="info_{{ $method['code'] }}" style="{{ $index === 0 ? '' : 'display:none;' }}">
                                    @if($method['code'] === 'transfer_bank')
                                        <p><strong>Bank:</strong> {{ $method['info']['bank_name'] }}</p>
                                        <p><strong>Rekening:</strong> {{ $method['info']['account_number'] }}</p>
                                        <p><strong>Atas Nama:</strong> {{ $method['info']['account_name'] }}</p>
                                        <p class="note">{{ $method['info']['note'] }}</p>
                                    @elseif($method['code'] === 'e_wallet')
                                        <p><strong>Nomor:</strong> {{ $method['info']['phone_number'] }}</p>
                                        <p><img src="{{ $method['info']['qr_code'] }}" alt="QR Code" style="max-width: 150px; margin-top: 10px;"></p>
                                    @elseif($method['code'] === 'cash')
                                        <p><strong>Kontak:</strong> {{ $method['info']['phone_number'] }}</p>
                                        <p><strong>Alamat:</strong> {{ $method['info']['address'] }}</p>
                                        <p><strong>PIC:</strong> {{ $method['info']['pic_name'] }}</p>
                                        <p><strong>Jam Kerja:</strong> {{ $method['info']['office_hours'] }}</p>
                                    @elseif($method['code'] === 'qris')
                                        <p><img src="{{ $method['info']['qr_code'] }}" alt="QRIS" style="max-width: 150px; margin-top: 10px;"></p>
                                        <p class="note">{{ $method['info']['note'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Upload Proof (optional) -->
                    <div class="form-group">
                        <label for="proof_file">Bukti Pembayaran (Opsional)</label>
                        <input type="file" name="proof_file" id="proof_file" accept=".pdf,.jpg,.jpeg,.png,.webp">
                        <small>Format: PDF, JPG, PNG (Max 5MB)</small>
                    </div>

                    <button type="submit" class="btn btn-submit">Kirim Pembayaran</button>
                </form>
            </div>
        @endif
    </div>
</div>

<style>
    .payment-container {
        min-height: 100vh;
        padding: 40px 20px;
        background: #f5f5f5;
    }

    .payment-wrapper {
        max-width: 700px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        padding: 40px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 30px;
        text-align: center;
    }

    .alert {
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .alert-success {
        background: #d4edda;
        border: 1px solid #b1dfbb;
        color: #155724;
    }

    .payment-info-card,
    .payment-breakdown-card,
    .payment-methods-card {
        background: #f9f9f9;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .payment-info-card h2,
    .payment-breakdown-card h2,
    .payment-methods-card h2 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e8e8e8;
    }

    .breakdown-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .breakdown-table th,
    .breakdown-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .breakdown-table th {
        background: #f0f0f0;
        font-weight: 600;
    }

    .breakdown-summary {
        background: white;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 15px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
    }

    .summary-row.total {
        border-top: 2px solid #ddd;
        padding-top: 15px;
        margin-top: 15px;
        font-size: 16px;
        font-weight: 700;
        color: #667eea;
    }

    .payment-methods {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 20px;
    }

    .payment-method-option {
        position: relative;
    }

    .payment-method-option input[type="radio"] {
        display: none;
    }

    .method-label {
        display: block;
        padding: 15px;
        border: 2px solid #ddd;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .payment-method-option input[type="radio"]:checked + .method-label {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .method-name {
        font-weight: 600;
        color: #333;
        display: block;
    }

    .method-desc {
        font-size: 12px;
        color: #666;
        display: block;
        margin-top: 4px;
    }

    .method-info {
        padding: 15px;
        background: white;
        border-radius: 6px;
        border-left: 3px solid #667eea;
        margin-top: 10px;
        font-size: 13px;
    }

    .method-info p {
        margin: 6px 0;
    }

    .method-info .note {
        background: #e3f2fd;
        padding: 8px;
        border-radius: 4px;
        color: #1565c0;
        margin-top: 10px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }

    .form-group input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
    }

    .form-group small {
        display: block;
        color: #999;
        margin-top: 6px;
        font-size: 12px;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #667eea;
        color: white;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary:hover {
        background: #5568d3;
    }

    .btn-submit {
        width: 100%;
        background: #4caf50;
        color: white;
    }

    .btn-submit:hover {
        background: #45a049;
    }

    @media (max-width: 600px) {
        .payment-wrapper {
            padding: 20px;
        }

        .page-title {
            font-size: 20px;
        }

        .breakdown-table {
            font-size: 12px;
        }

        .breakdown-table th,
        .breakdown-table td {
            padding: 8px;
        }
    }
</style>

<script>
    function updateMethodInfo(radio) {
        // Hide all method infos
        document.querySelectorAll('.method-info').forEach(el => el.style.display = 'none');
        // Show selected method info
        document.getElementById('info_' + radio.value).style.display = 'block';
    }
</script>
@endsection
