@php
    $studentPayment = $studentPayment ?? null;
    $sp = $studentPayment;

    $modalId = 'student-payment-installments-reset-modal';
    $finalAmount = (float)($sp?->final_amount ?? 0);

    $studentName = $sp?->student?->name ?? '-';
    $paymentName = $sp?->payment?->name ?? '-';
    $period = $sp?->payment_period ?? '-';

    $installments = $sp?->installments;
    if (!($installments instanceof \Illuminate\Support\Collection)) {
        $installments = collect($installments ?? []);
    }

    $count = $installments->count();
@endphp

<div id="{{ $modalId }}" class="modal-overlay" hidden aria-hidden="true" data-modal-type="student-payment">
    <div class="modal modal-admin modal-admin-form" role="dialog" aria-modal="true" aria-labelledby="installment-reset-title">
        <div class="modal-header">
            <h2 id="installment-reset-title" class="modal-title">Reset Cicilan</h2>
            <button type="button" class="modal-close" data-modal-close="close-modal" aria-label="Tutup">✕</button>
        </div>

        <form class="admin-modal-form" method="POST" action="{{ route('admin.student-payments.installments.reset', $sp) }}">
            @csrf
            @method('DELETE')

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Ringkasan</label>
                    <div class="registration-detail-row"><span>Murid</span><strong>{{ $studentName }}</strong></div>
                    <div class="registration-detail-row"><span>Payment</span><strong>{{ $paymentName }}</strong></div>
                    <div class="registration-detail-row"><span>Periode</span><strong>{{ $period }}</strong></div>
                    <div class="registration-detail-row"><span>Final</span><strong>Rp {{ number_format($finalAmount, 0, ',', '.') }}</strong></div>
                    <div class="registration-detail-row"><span>Jumlah Cicilan Saat Ini</span><strong>{{ $count }}</strong></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi</label>
                    <span class="form-label-hint">
                        Tindakan ini akan menghapus semua cicilan (yang masih Pending) untuk tagihan ini.
                        Setelah reset, Anda bisa membuat jadwal cicilan baru.
                    </span>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="admin-btn admin-btn-cancel" data-modal-close="close-modal">Batal</button>
                <button type="submit" class="admin-btn admin-btn-submit">Reset Cicilan</button>
            </div>
        </form>
    </div>
</div>
