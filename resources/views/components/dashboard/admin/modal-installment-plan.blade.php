@php
    $studentPayment = $studentPayment ?? null;
    $sp = $studentPayment;

    $modalId = 'student-payment-installments-plan-modal';
    $finalAmount = (float)($sp?->final_amount ?? 0);

    $studentName = $sp?->student?->name ?? '-';
    $paymentName = $sp?->payment?->name ?? '-';
    $period = $sp?->payment_period ?? '-';

    $defaultCount = old('installment_count') ?? ($sp?->installment_count ?? 3);
    $defaultFirstDue = old('first_due_date') ?? now()->toDateString();
    $defaultInterval = old('interval_months') ?? 1;
@endphp

<div id="{{ $modalId }}" class="modal-overlay" hidden aria-hidden="true" data-modal-type="student-payment">
    <div class="modal modal-admin modal-admin-form" role="dialog" aria-modal="true" aria-labelledby="installment-plan-title">
        <div class="modal-header">
            <h2 id="installment-plan-title" class="modal-title">Atur Cicilan Tagihan</h2>
            <button type="button" class="modal-close" data-modal-close="close-modal" aria-label="Tutup">✕</button>
        </div>

        <form class="admin-modal-form" method="POST" action="{{ route('admin.student-payments.installments.store', $sp) }}" enctype="multipart/form-data">
            @csrf

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Ringkasan</label>
                    <div class="registration-detail-row"><span>Murid</span><strong>{{ $studentName }}</strong></div>
                    <div class="registration-detail-row"><span>Payment</span><strong>{{ $paymentName }}</strong></div>
                    <div class="registration-detail-row"><span>Periode</span><strong>{{ $period }}</strong></div>
                    <div class="registration-detail-row"><span>Final</span><strong>Rp {{ number_format($finalAmount, 0, ',', '.') }}</strong></div>
                </div>

                <div class="form-group">
                    <label for="installment-count" class="form-label">Jumlah Cicilan</label>
                    <input
                        type="number"
                        id="installment-count"
                        name="installment_count"
                        class="form-input"
                        value="{{ $defaultCount }}"
                        min="2"
                        max="24"
                        step="1"
                        required
                    >
                    @error('installment_count')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="first-due-date" class="form-label">Jatuh Tempo Cicilan Pertama</label>
                    <input
                        type="date"
                        id="first-due-date"
                        name="first_due_date"
                        class="form-input"
                        value="{{ $defaultFirstDue }}"
                        required
                    >
                    @error('first_due_date')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="interval-months" class="form-label">Jarak Per Cicilan (bulan)</label>
                    <input
                        type="number"
                        id="interval-months"
                        name="interval_months"
                        class="form-input"
                        value="{{ $defaultInterval }}"
                        min="1"
                        max="12"
                        step="1"
                    >
                    @error('interval_months')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                    <span class="form-label-hint">Contoh: 1 = setiap bulan.</span>
                </div>

                <span class="form-label-hint">Catatan: nominal cicilan dibagi merata. Selisih pembulatan ditambahkan ke cicilan terakhir.</span>
            </div>

            <div class="modal-footer">
                <button type="button" class="admin-btn admin-btn-cancel" data-modal-close="close-modal">Batal</button>
                <button type="submit" class="admin-btn admin-btn-submit">Buat Cicilan</button>
            </div>
        </form>
    </div>
</div>
