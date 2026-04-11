@php
    $studentPayment = $studentPayment ?? null;
    $installment = $installment ?? null;

    $sp = $studentPayment;
    $ins = $installment;

    $modalId = 'student-payment-installment-pay-modal';

    $studentName = $sp?->student?->name ?? '-';
    $paymentName = $sp?->payment?->name ?? '-';
    $period = $sp?->payment_period ?? '-';

    $method = old('payment_method') ?? ($ins?->payment_method ?? '');
@endphp

<div id="{{ $modalId }}" class="modal-overlay" hidden aria-hidden="true" data-modal-type="student-payment">
    <div class="modal modal-admin modal-admin-form" role="dialog" aria-modal="true" aria-labelledby="installment-pay-title">
        <div class="modal-header">
            <h2 id="installment-pay-title" class="modal-title">Bayar Cicilan</h2>
            <button type="button" class="modal-close" data-modal-close="close-modal" aria-label="Tutup">✕</button>
        </div>

        <form class="admin-modal-form" method="POST" action="{{ route('admin.student-payments.installments.pay.update', [$sp, $ins]) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Ringkasan</label>
                    <div class="registration-detail-row"><span>Murid</span><strong>{{ $studentName }}</strong></div>
                    <div class="registration-detail-row"><span>Payment</span><strong>{{ $paymentName }}</strong></div>
                    <div class="registration-detail-row"><span>Periode</span><strong>{{ $period }}</strong></div>
                    <div class="registration-detail-row"><span>Cicilan Ke-</span><strong>{{ $ins?->installment_number ?? '-' }}</strong></div>
                    <div class="registration-detail-row"><span>Jatuh Tempo</span><strong>{{ $ins?->due_date?->format('Y-m-d') ?? '-' }}</strong></div>
                    <div class="registration-detail-row"><span>Nominal</span><strong>Rp {{ number_format((float)($ins?->installment_amount ?? 0), 0, ',', '.') }}</strong></div>
                </div>

                <div class="form-group">
                    <label for="installment-method" class="form-label">Metode Pembayaran</label>
                    <select id="installment-method" name="payment_method" class="form-input form-select" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="transfer_bank" @selected($method === 'transfer_bank')>Transfer Bank</option>
                        <option value="e_wallet" @selected($method === 'e_wallet')>E-Wallet</option>
                        <option value="cash" @selected($method === 'cash')>Cash</option>
                        <option value="qris" @selected($method === 'qris')>QRIS</option>
                    </select>
                    @error('payment_method')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="installment-proof" class="form-label">Bukti Bayar (Opsional)</label>
                    <input type="file" id="installment-proof" name="proof_file" class="form-input">
                    @error('proof_file')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                    <span class="form-label-hint">Upload bukti bayar (maks 4MB).</span>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="admin-btn admin-btn-cancel" data-modal-close="close-modal">Batal</button>
                <button type="submit" class="admin-btn admin-btn-submit">Tandai Paid</button>
            </div>
        </form>
    </div>
</div>
