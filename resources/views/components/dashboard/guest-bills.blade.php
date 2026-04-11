@php
    /** @var \Illuminate\Support\Collection|array $studentPayments */
    $studentPayments = $studentPayments ?? collect();
    /** @var \Illuminate\Support\Collection|array $paymentMethods */
    $paymentMethods = $paymentMethods ?? collect();
    $paymentSettings = $paymentSettings ?? null;

    $normalizeProofUrl = function (?string $path): ?string {
        $path = (string)($path ?? '');
        if ($path === '') return null;

        if (preg_match('#^https?://#i', $path)) return $path;

        $clean = ltrim($path, '/');

        // Legacy: already stored as public URL path (storage symlink)
        if (str_starts_with($clean, 'storage/')) {
            return asset($clean);
        }

        // Legacy: sometimes stored as "public/..." even though disk is public
        if (str_starts_with($clean, 'public/')) {
            $clean = substr($clean, strlen('public/'));
        }

        return \Illuminate\Support\Facades\Storage::url($clean);
    };

    $detectProofKind = function (?string $urlOrPath): string {
        $value = (string)($urlOrPath ?? '');
        $path = parse_url($value, PHP_URL_PATH) ?: $value;
        $path = strtolower((string)$path);
        return str_ends_with($path, '.pdf') ? 'pdf' : 'image';
    };
@endphp

<style>
    .guest-bills {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border: 2px solid var(--green-light);
        margin-bottom: 32px;
    }

    .guest-bills-layout {
        display: grid;
        grid-template-columns: 65% 35%;
        grid-template-areas: "main side";
        gap: 16px;
        align-items: start;
    }

    .guest-bills-main {
        grid-area: main;
        min-width: 0;
    }

    .guest-bills-side {
        grid-area: side;
        min-width: 0;
    }

    .guest-bills-payment-panel {
        background: var(--bg);
        border-radius: 14px;
        padding: 14px;
        border: 2px solid var(--green-light);
    }

    .guest-bills-panel-title {
        font-family: 'Fredoka One', cursive;
        font-size: 16px;
        color: var(--dark);
        margin: 0 0 10px 0;
    }

    .guest-bills-panel-subtitle {
        color: var(--gray);
        font-size: 12px;
        font-weight: 700;
        margin: 0 0 12px 0;
        line-height: 1.35;
    }

    .payment-info-section-title {
        font-size: 12px;
        font-weight: 900;
        color: var(--gray);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin: 10px 0 8px;
    }

    .payment-info-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px dashed rgba(46, 204, 113, 0.25);
    }

    .payment-info-row:last-child {
        border-bottom: none;
    }

    .payment-info-row span {
        color: var(--gray);
        font-weight: 800;
        font-size: 13px;
    }

    .payment-info-row strong {
        color: var(--dark);
        font-weight: 900;
        font-size: 13px;
        text-align: right;
        overflow-wrap: anywhere;
    }

    .payment-info-divider {
        height: 2px;
        background: var(--green-light);
        margin: 12px 0;
        border-radius: 2px;
    }

    .payment-info-qris {
        width: 100%;
        border-radius: 12px;
        border: 2px solid var(--green-light);
        background: white;
        display: block;
    }

    .guest-bills-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .guest-bills-title {
        font-family: 'Fredoka One', cursive;
        font-size: 18px;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .guest-bills-subtitle {
        color: var(--gray);
        font-size: 13px;
        font-weight: 700;
        margin-top: 4px;
    }

    .guest-bills-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 14px;
    }

    .guest-bills-alerts {
        display: grid;
        gap: 10px;
        margin-bottom: 16px;
    }

    .guest-bills-alert {
        background: white;
        border-radius: 14px;
        padding: 12px 14px;
        border: 2px solid var(--green-light);
        font-size: 13px;
        font-weight: 800;
        color: var(--dark);
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .guest-bills-alert .guest-bills-alert-icon {
        flex: 0 0 auto;
        margin-top: 1px;
    }

    .guest-bills-alert .guest-bills-alert-text {
        flex: 1 1 auto;
        line-height: 1.35;
    }

    .guest-bills-alert.warning {
        background: #fff8e1;
        border-color: #ffe7a0;
    }

    .guest-bills-alert.info {
        background: var(--bg);
        border-color: var(--green-light);
    }

    .guest-bills-alert.error {
        background: #fee2e2;
        border-color: #fecaca;
    }

    .bill-card {
        background: var(--bg);
        border-radius: 14px;
        padding: 16px;
        border-left: 4px solid var(--green);
    }

    .bill-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .bill-title {
        font-weight: 900;
        color: var(--dark);
        font-size: 14px;
        line-height: 1.25;
    }

    .bill-period {
        font-size: 12px;
        color: var(--gray);
        font-weight: 700;
        margin-top: 4px;
    }

    .bill-amount {
        font-size: 15px;
        font-weight: 900;
        color: var(--green-dark);
        margin: 8px 0 6px;
    }

    .bill-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
        background: white;
        border: 2px solid var(--green-light);
        color: var(--dark);
    }

    .bill-badge.pending {
        background: #fff8e1;
        border-color: #ffe7a0;
        color: #a16207;
    }

    .bill-badge.paid {
        background: #d4f5e3;
        border-color: #b7f0d3;
        color: var(--green-dark);
    }

    .bill-badge.failed {
        background: #fee2e2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .bill-meta {
        display: grid;
        grid-template-columns: 1fr;
        gap: 6px;
        margin-top: 10px;
    }

    .bill-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-size: 12px;
    }

    .bill-meta-row span {
        color: var(--gray);
        font-weight: 800;
    }

    .bill-meta-row strong {
        color: var(--dark);
        font-weight: 900;
        text-align: right;
    }

    .bill-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 12px;
    }

    .bill-proof-link {
        font-size: 12px;
        font-weight: 900;
        color: var(--green-dark);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 14px;
        border-radius: 999px;
        border: 2px solid var(--green-light);
        background: white;
    }

    .bill-proof-link:hover {
        background: var(--green-light);
    }

    .bill-proof-preview {
        width: 100%;
        height: min(60vh, 520px);
        border: 2px solid var(--green-light);
        border-radius: 14px;
        background: white;
        overflow: hidden;
    }

    .bill-proof-preview iframe {
        width: 100%;
        height: 100%;
        border: 0;
    }

    .bill-proof-preview img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
        background: white;
    }

    .bill-detail-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .bill-detail-section {
        background: var(--bg);
        border-radius: 14px;
        padding: 14px;
        border-left: 4px solid var(--green);
    }

    .bill-detail-title {
        font-weight: 900;
        color: var(--dark);
        margin: 0 0 10px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .bill-detail-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .bill-detail-table td {
        padding: 8px 0;
        border-bottom: 1px dashed var(--green-light);
        vertical-align: top;
    }

    .bill-detail-table tr:last-child td {
        border-bottom: 0;
    }

    .bill-detail-table td:first-child {
        color: var(--gray);
        font-weight: 800;
        padding-right: 12px;
        width: 55%;
    }

    .bill-detail-table td:last-child {
        color: var(--dark);
        font-weight: 900;
        text-align: right;
    }

    .bill-detail-installments {
        display: grid;
        gap: 10px;
    }

    .bill-detail-installment {
        background: white;
        border-radius: 12px;
        padding: 12px;
        border: 2px solid var(--green-light);
    }

    .bill-detail-installment-top {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: flex-start;
    }

    .bill-detail-installment-name {
        font-weight: 900;
        color: var(--dark);
        font-size: 13px;
    }

    .bill-detail-installment-meta {
        margin-top: 8px;
        display: grid;
        gap: 6px;
        font-size: 12px;
    }

    .bill-detail-installment-meta-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .bill-detail-installment-meta-row span {
        color: var(--gray);
        font-weight: 800;
    }

    .bill-detail-installment-meta-row strong {
        color: var(--dark);
        font-weight: 900;
        text-align: right;
    }

    .bill-installment-note {
        margin-top: 10px;
        font-size: 12px;
        color: var(--gray);
        font-weight: 700;
    }

    .guest-bills-empty {
        background: white;
        border-radius: 14px;
        padding: 18px;
        border: 2px dashed var(--green-light);
        color: var(--gray);
        font-size: 13px;
        font-weight: 700;
    }

    @media (max-width: 600px) {
        .guest-bills {
            padding: 16px;
        }

        .guest-bills-list {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 900px) {
        .guest-bills-layout {
            grid-template-columns: 1fr;
            grid-template-areas:
                "side"
                "main";
        }
    }
</style>

<div id="guest-bills" class="guest-bills">
    <div class="guest-bills-header">
        <div>
            <h3 class="guest-bills-title">🧾 Tagihan Anda</h3>
            <div class="guest-bills-subtitle">Daftar tagihan pembayaran untuk siswa yang terdaftar pada akun ini.</div>
        </div>
    </div>

    <div class="guest-bills-layout">
        <div class="guest-bills-side">
            @php
                $methods = collect($paymentMethods ?? []);
                $bankMethods = $methods->where('type', 'bank')->values();
                $ewalletMethods = $methods->where('type', 'ewallet')->values();
                $otherMethods = $methods->where('type', 'other')->values();
                $qrisMethod = $methods->firstWhere('type', 'qris');

                // Legacy fallback if payment_methods not yet populated.
                if ($methods->count() === 0 && $paymentSettings) {
                    $legacyBankName = trim((string)($paymentSettings?->bank_name ?? ''));
                    $legacyBankAcc = trim((string)($paymentSettings?->bank_account_number ?? ''));
                    $legacyBankHolder = trim((string)($paymentSettings?->bank_account_holder ?? ''));

                    if ($legacyBankName !== '' || $legacyBankAcc !== '' || $legacyBankHolder !== '') {
                        $bankMethods = collect([
                            (object)[
                                'label' => $legacyBankName !== '' ? $legacyBankName : 'Transfer Bank',
                                'account_number' => $legacyBankAcc !== '' ? $legacyBankAcc : null,
                                'account_name' => $legacyBankHolder !== '' ? $legacyBankHolder : null,
                                'description' => null,
                            ],
                        ]);
                    }

                    $legacyQrisPath = (string)($paymentSettings?->qris_image_path ?? '');
                    if ($legacyQrisPath !== '') {
                        $qrisMethod = (object)['image_path' => $legacyQrisPath];
                    }
                }

                $qrisPath = trim((string)($qrisMethod->image_path ?? ''));
            @endphp

            <div class="guest-bills-payment-panel">
                <h4 class="guest-bills-panel-title">Informasi Pembayaran</h4>
                <p class="guest-bills-panel-subtitle">Gunakan info berikut untuk melakukan pembayaran. Pastikan data sesuai sebelum mengirim bukti.</p>

                <div class="payment-info-section-title">Transfer Bank</div>
                @if(($bankMethods->count() ?? 0) === 0)
                    <div class="payment-info-row"><span>Bank</span><strong>-</strong></div>
                @else
                    @foreach($bankMethods as $method)
                        <div class="payment-info-row"><span>Bank</span><strong>{{ trim((string)($method->label ?? '')) !== '' ? $method->label : '-' }}</strong></div>
                        <div class="payment-info-row"><span>No Rekening</span><strong>{{ trim((string)($method->account_number ?? '')) !== '' ? $method->account_number : '-' }}</strong></div>
                        <div class="payment-info-row"><span>Atas Nama</span><strong>{{ trim((string)($method->account_name ?? '')) !== '' ? $method->account_name : '-' }}</strong></div>
                        @if(trim((string)($method->description ?? '')) !== '')
                            <div class="payment-info-row"><span>Catatan</span><strong>{{ $method->description }}</strong></div>
                        @endif
                        @if(!$loop->last)
                            <div class="payment-info-divider"></div>
                        @endif
                    @endforeach
                @endif

                @if(($ewalletMethods->count() ?? 0) > 0)
                    <div class="payment-info-divider"></div>
                    <div class="payment-info-section-title">E-Wallet</div>
                    @foreach($ewalletMethods as $method)
                        <div class="payment-info-row"><span>Provider</span><strong>{{ trim((string)($method->label ?? '')) !== '' ? $method->label : '-' }}</strong></div>
                        <div class="payment-info-row"><span>Nomor / ID</span><strong>{{ trim((string)($method->account_number ?? '')) !== '' ? $method->account_number : '-' }}</strong></div>
                        @if(trim((string)($method->account_name ?? '')) !== '')
                            <div class="payment-info-row"><span>Atas Nama</span><strong>{{ $method->account_name }}</strong></div>
                        @endif
                        @if(trim((string)($method->description ?? '')) !== '')
                            <div class="payment-info-row"><span>Catatan</span><strong>{{ $method->description }}</strong></div>
                        @endif
                        @if(!$loop->last)
                            <div class="payment-info-divider"></div>
                        @endif
                    @endforeach
                @endif

                @if(($otherMethods->count() ?? 0) > 0)
                    <div class="payment-info-divider"></div>
                    <div class="payment-info-section-title">Lainnya</div>
                    @foreach($otherMethods as $method)
                        <div class="payment-info-row"><span>Metode</span><strong>{{ trim((string)($method->label ?? '')) !== '' ? $method->label : '-' }}</strong></div>
                        @if(trim((string)($method->description ?? '')) !== '')
                            <div class="payment-info-row"><span>Keterangan</span><strong>{{ $method->description }}</strong></div>
                        @endif
                        @if(!$loop->last)
                            <div class="payment-info-divider"></div>
                        @endif
                    @endforeach
                @endif

                <div class="payment-info-divider"></div>

                <div class="payment-info-section-title">QRIS</div>
                @if($qrisPath !== '')
                    <img class="payment-info-qris" src="{{ asset($qrisPath) }}" alt="QRIS" />
                @else
                    <div class="payment-info-row"><span>QRIS</span><strong>-</strong></div>
                @endif
            </div>
        </div>

        <div class="guest-bills-main">

    @php
        $allPayments = collect($studentPayments);

        $failedCount = $allPayments->where('status', 'failed')->count();
        $pendingNoProofCount = $allPayments->filter(function ($sp) {
            $statusValue = (string)($sp->status ?? 'pending');
            if ($statusValue === 'paid') return false;
            $installmentTotal = collect($sp->installments ?? [])->count();
            $proofs = collect($sp->proofs ?? []);
            $hasProof = $proofs->count() > 0 || (bool)($sp->proof_file ?? null);
            return $installmentTotal === 0 && !$hasProof;
        })->count();

        $pendingWithProofCount = $allPayments->filter(function ($sp) {
            $statusValue = (string)($sp->status ?? 'pending');
            $installmentTotal = collect($sp->installments ?? [])->count();
            $proofs = collect($sp->proofs ?? []);
            $hasProof = $proofs->count() > 0 || (bool)($sp->proof_file ?? null);
            return $installmentTotal === 0 && $statusValue !== 'paid' && $hasProof;
        })->count();

        $installmentPendingWithProofCount = $allPayments->reduce(function ($carry, $sp) {
            $installments = collect($sp->installments ?? []);
            $count = $installments->filter(function ($i) {
                $statusValue = (string)($i->status ?? 'pending');
                $proofs = collect($i->proofs ?? []);
                $hasProof = $proofs->count() > 0 || (bool)($i->proof_file ?? null);
                return $statusValue !== 'paid' && $hasProof;
            })->count();
            return $carry + $count;
        }, 0);
    @endphp

    @if($failedCount > 0 || $pendingNoProofCount > 0 || $pendingWithProofCount > 0 || $installmentPendingWithProofCount > 0)
        <div class="guest-bills-alerts">
            @if($pendingNoProofCount > 0)
                <div class="guest-bills-alert warning">
                    <div class="guest-bills-alert-icon">⏳</div>
                    <div class="guest-bills-alert-text">
                        Ada {{ $pendingNoProofCount }} tagihan menunggu pembayaran. Silakan klik <strong>Bayar</strong> untuk upload bukti.
                    </div>
                </div>
            @endif

            @if($pendingWithProofCount > 0 || $installmentPendingWithProofCount > 0)
                <div class="guest-bills-alert info">
                    <div class="guest-bills-alert-icon">📎</div>
                    <div class="guest-bills-alert-text">
                        Ada bukti pembayaran yang sudah dikirim dan <strong>menunggu verifikasi admin</strong>.
                    </div>
                </div>
            @endif

            @if($failedCount > 0)
                <div class="guest-bills-alert error">
                    <div class="guest-bills-alert-icon">⚠️</div>
                    <div class="guest-bills-alert-text">
                        Ada {{ $failedCount }} tagihan berstatus <strong>Gagal</strong>. Silakan hubungi admin atau kirim ulang bukti pembayaran jika diperlukan.
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if(collect($studentPayments)->count() === 0)
        <div class="guest-bills-empty">Belum ada tagihan yang dibuat untuk akun Anda.</div>
    @else
        <div class="guest-bills-list">
            @foreach($studentPayments as $sp)
                @php
                    $paymentName = $sp->payment?->name ?? '-';
                    $period = $sp->payment_period ?? '-';
                    $amountLabel = 'Rp ' . number_format((float)($sp->final_amount ?? 0), 0, ',', '.');
                    $totalLabel = 'Rp ' . number_format((float)($sp->total_amount ?? 0), 0, ',', '.');
                    $discountLabel = 'Rp ' . number_format((float)($sp->discount_amount ?? 0), 0, ',', '.');

                    $statusValue = (string)($sp->status ?? 'pending');

                    $headerProofs = collect($sp->proofs ?? []);
                    $latestHeaderProof = $headerProofs->first();
                    $latestHeaderProofPath = $headerProofs->first()?->file_path ?? ($sp->proof_file ?? null);
                    $hasProof = (bool)($latestHeaderProofPath ?? null);
                    $hasPendingHeaderProof = $headerProofs->contains(fn($p) => (string)($p?->status ?? '') === 'pending');
                    $statusLabel = match($statusValue) {
                        'pending' => $hasProof ? 'Menunggu Verifikasi' : 'Menunggu',
                        'paid' => 'Lunas',
                        'failed' => 'Gagal',
                        default => $statusValue,
                    };

                    $paymentMethodLabel = $sp->payment_method
                        ? strtoupper(str_replace('_', ' ', (string)$sp->payment_method))
                        : '-';

                    if ($latestHeaderProof && ($latestHeaderProof->payment_method_label ?? null)) {
                        $extra = [];
                        $extraLabel = trim((string)($latestHeaderProof->payment_method_label ?? ''));
                        $extraAcc = trim((string)($latestHeaderProof->payment_method_account_number ?? ''));
                        $extraName = trim((string)($latestHeaderProof->payment_method_account_name ?? ''));
                        if ($extraLabel !== '') $extra[] = $extraLabel;
                        if ($extraAcc !== '') $extra[] = $extraAcc;
                        if ($extraName !== '') $extra[] = 'a.n ' . $extraName;
                        if (!empty($extra)) {
                            $paymentMethodLabel = $paymentMethodLabel . ' • ' . implode(' • ', $extra);
                        }
                    }
                    $paidAtLabel = $sp->paid_at ? $sp->paid_at->format('Y-m-d') : '-';

                    $installments = $sp->installments ?? collect();
                    $installmentTotal = collect($installments)->count();
                    $installmentPaid = collect($installments)->where('status', 'paid')->count();
                    $nextDue = collect($installments)->where('status', 'pending')->sortBy('due_date')->first();
                    $payableInstallment = $nextDue;
                    $nextDueAmountLabel = $payableInstallment
                        ? ('Rp ' . number_format((float)($payableInstallment->installment_amount ?? 0), 0, ',', '.'))
                        : null;

                    $snapshot = is_array($sp->detail_fee_snapshot ?? null) ? $sp->detail_fee_snapshot : [];
                    $snapshotRows = collect($snapshot)->filter(fn($r) => is_array($r))->values();

                    $installmentPayload = collect($installments)->map(function ($inst) use ($normalizeProofUrl) {
                        $proofs = collect($inst->proofs ?? []);
                        $latestPath = $proofs->first()?->file_path ?? ($inst->proof_file ?? null);
                        $proofUrl = $normalizeProofUrl($latestPath);
                        $proofKind = 'image';
                        if ($proofUrl) {
                            $path = parse_url($proofUrl, PHP_URL_PATH) ?: $proofUrl;
                            $proofKind = str_ends_with(strtolower((string)$path), '.pdf') ? 'pdf' : 'image';
                        }

                        $history = $proofs->map(function ($p) use ($normalizeProofUrl) {
                            $url = $normalizeProofUrl($p->file_path ?? null);
                            $kind = 'image';
                            if ($url) {
                                $path = parse_url($url, PHP_URL_PATH) ?: $url;
                                $kind = str_ends_with(strtolower((string)$path), '.pdf') ? 'pdf' : 'image';
                            }

                            $createdAt = $p->created_at ?? null;
                            $createdAtLabel = '-';
                            if ($createdAt && is_object($createdAt) && method_exists($createdAt, 'format')) {
                                $createdAtLabel = $createdAt->format('Y-m-d H:i');
                            }

                            $base = $p->payment_method
                                ? strtoupper(str_replace('_', ' ', (string)$p->payment_method))
                                : '-';

                            $extra = [];
                            $extraLabel = trim((string)($p->payment_method_label ?? ''));
                            $extraAcc = trim((string)($p->payment_method_account_number ?? ''));
                            $extraName = trim((string)($p->payment_method_account_name ?? ''));
                            if ($extraLabel !== '') $extra[] = $extraLabel;
                            if ($extraAcc !== '') $extra[] = $extraAcc;
                            if ($extraName !== '') $extra[] = 'a.n ' . $extraName;

                            return [
                                'uploaded_at' => $createdAtLabel,
                                'payment_method' => !empty($extra) ? ($base . ' • ' . implode(' • ', $extra)) : $base,
                                'status' => (string)($p->status ?? 'pending'),
                                'admin_note' => (string)($p->admin_note ?? ''),
                                'proof_url' => $url,
                                'proof_kind' => $kind,
                            ];
                        })->values()->all();

                        return [
                            'number' => $inst->installment_number ?? null,
                            'due_date' => $inst->due_date ?? null,
                            'amount' => (float)($inst->installment_amount ?? 0),
                            'amount_label' => 'Rp ' . number_format((float)($inst->installment_amount ?? 0), 0, ',', '.'),
                            'status' => (string)($inst->status ?? 'pending'),
                            'payment_method' => $inst->payment_method
                                ? strtoupper(str_replace('_', ' ', (string)$inst->payment_method))
                                : '-',
                            'paid_at' => $inst->paid_at ? $inst->paid_at->format('Y-m-d') : '-',
                            'proof_url' => $proofUrl,
                            'proof_kind' => $proofKind,
                            'proof_history' => $history,
                        ];
                    })->values()->all();

                    $headerHistoryPayload = $headerProofs->map(function ($p) use ($normalizeProofUrl) {
                        $url = $normalizeProofUrl($p->file_path ?? null);
                        $kind = 'image';
                        if ($url) {
                            $path = parse_url($url, PHP_URL_PATH) ?: $url;
                            $kind = str_ends_with(strtolower((string)$path), '.pdf') ? 'pdf' : 'image';
                        }

                        $createdAt = $p->created_at ?? null;
                        $createdAtLabel = '-';
                        if ($createdAt && is_object($createdAt) && method_exists($createdAt, 'format')) {
                            $createdAtLabel = $createdAt->format('Y-m-d H:i');
                        }

                        $base = $p->payment_method
                            ? strtoupper(str_replace('_', ' ', (string)$p->payment_method))
                            : '-';

                        $extra = [];
                        $extraLabel = trim((string)($p->payment_method_label ?? ''));
                        $extraAcc = trim((string)($p->payment_method_account_number ?? ''));
                        $extraName = trim((string)($p->payment_method_account_name ?? ''));
                        if ($extraLabel !== '') $extra[] = $extraLabel;
                        if ($extraAcc !== '') $extra[] = $extraAcc;
                        if ($extraName !== '') $extra[] = 'a.n ' . $extraName;

                        return [
                            'uploaded_at' => $createdAtLabel,
                            'payment_method' => !empty($extra) ? ($base . ' • ' . implode(' • ', $extra)) : $base,
                            'status' => (string)($p->status ?? 'pending'),
                            'admin_note' => (string)($p->admin_note ?? ''),
                            'proof_url' => $url,
                            'proof_kind' => $kind,
                        ];
                    })->values()->all();

                    $detailPayload = [
                        'payment_name' => $paymentName,
                        'period' => (string)$period,
                        'status' => $statusValue,
                        'status_label' => $statusLabel,
                        'final_amount_label' => $amountLabel,
                        'total_amount_label' => $totalLabel,
                        'discount_amount_label' => $discountLabel,
                        'payment_method_label' => $paymentMethodLabel,
                        'paid_at_label' => $paidAtLabel,
                        'proof_url' => $hasProof ? $normalizeProofUrl($latestHeaderProofPath) : null,
                        'proof_kind' => $hasProof ? $detectProofKind($normalizeProofUrl($latestHeaderProofPath) ?? $latestHeaderProofPath) : null,
                        'proof_history' => $headerHistoryPayload,
                        'snapshot' => $snapshotRows->all(),
                        'installments' => $installmentPayload,
                    ];
                @endphp

                <div class="bill-card">
                    <div class="bill-top">
                        <div>
                            <div class="bill-title">{{ $paymentName }}</div>
                            <div class="bill-period">Periode: {{ $period }}</div>
                        </div>
                        <span class="bill-badge {{ $statusValue }}">
                            @if($statusValue === 'paid') ✓ @elseif($statusValue === 'failed') ✕ @else ⏳ @endif
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="bill-amount">{{ $amountLabel }}</div>

                    <div class="bill-meta">
                        @if($installmentTotal > 0)
                            <div class="bill-meta-row">
                                <span>Cicilan</span>
                                <strong>{{ $installmentPaid }}/{{ $installmentTotal }} terbayar</strong>
                            </div>
                            @if($nextDue)
                                <div class="bill-meta-row">
                                    <span>Jatuh tempo berikutnya</span>
                                    <strong>{{ $nextDue->due_date ?? '-' }}</strong>
                                </div>
                                <div class="bill-meta-row">
                                    <span>Nominal cicilan</span>
                                    <strong>{{ $nextDueAmountLabel }}</strong>
                                </div>
                            @endif
                        @else
                            <div class="bill-meta-row">
                                <span>Metode</span>
                                <strong>{{ $paymentMethodLabel }}</strong>
                            </div>
                            <div class="bill-meta-row">
                                <span>Dibayar</span>
                                <strong>{{ $sp->paid_at ? $sp->paid_at->format('Y-m-d') : '-' }}</strong>
                            </div>
                        @endif
                    </div>

                    <div class="bill-actions">
                        <button
                            type="button"
                            class="btn-secondary"
                            data-guest-detail-open
                            data-detail='@json($detailPayload)'
                        >📄 Detail</button>

                        @if(($statusValue ?? 'pending') !== 'paid')
                            @if($installmentTotal > 0)
                                @if($payableInstallment)
                                    @php
                                        $payableProofs = collect($payableInstallment->proofs ?? []);
                                        $payableLatestProof = $payableProofs->first();
                                        $payableLatestProofPath = $payableLatestProof?->file_path ?? ($payableInstallment->proof_file ?? null);
                                        $installmentHasProof = (bool)($payableLatestProofPath ?? null);
                                        $installmentHasPendingProof = $payableProofs->contains(fn($p) => (string)($p?->status ?? '') === 'pending');
                                        $installmentButtonLabel = $installmentHasPendingProof
                                            ? 'Menunggu Verifikasi'
                                            : ($installmentHasProof ? 'Upload Bukti (Update)' : 'Upload Bukti');
                                        $installmentProofUrl = $installmentHasProof ? $normalizeProofUrl($payableLatestProofPath) : null;
                                        $installmentProofKind = $detectProofKind($installmentProofUrl ?? $payableLatestProofPath);
                                    @endphp

                                    @if($installmentHasPendingProof)
                                        <button
                                            type="button"
                                            class="btn-secondary"
                                            disabled
                                            aria-disabled="true"
                                        >⏳ {{ $installmentButtonLabel }}</button>
                                    @else
                                        <button
                                            type="button"
                                            class="btn-secondary"
                                            data-guest-pay-open
                                            data-pay-url="{{ route('dashboard.bills.installments.pay', [$sp, $payableInstallment]) }}"
                                            data-pay-title="{{ $paymentName }} — Cicilan #{{ $payableInstallment->installment_number ?? '-' }}"
                                            data-pay-subtitle="Jatuh tempo {{ $payableInstallment->due_date ?? '-' }} • {{ $nextDueAmountLabel }}"
                                        >💳 {{ $installmentButtonLabel }}</button>
                                    @endif

                                    @if($installmentHasProof && $installmentProofUrl)
                                        <button
                                            type="button"
                                            class="bill-proof-link"
                                            data-guest-proof-open
                                            data-proof-url="{{ $installmentProofUrl }}"
                                            data-proof-kind="{{ $installmentProofKind }}"
                                            data-proof-title="{{ $paymentName }} — Bukti Cicilan #{{ $payableInstallment->installment_number ?? '-' }}"
                                        >📎 Lihat Bukti</button>
                                    @endif
                                @endif
                                <div class="bill-installment-note">Pembayaran cicilan dilakukan dengan upload bukti untuk cicilan yang jatuh tempo.</div>
                            @else
                                @if($hasPendingHeaderProof)
                                    <button
                                        type="button"
                                        class="btn-secondary"
                                        disabled
                                        aria-disabled="true"
                                    >⏳ Menunggu Verifikasi</button>
                                @else
                                    <button
                                        type="button"
                                        class="btn-primary"
                                        data-guest-pay-open
                                        data-pay-url="{{ route('dashboard.bills.pay', $sp) }}"
                                        data-pay-title="{{ $paymentName }}"
                                        data-pay-subtitle="Periode {{ $period }} • {{ $amountLabel }}"
                                    >💳 Bayar</button>
                                @endif
                            @endif
                        @endif

                        @if($hasProof)
                            @php
                                $proofUrl = $normalizeProofUrl($latestHeaderProofPath);
                                $proofKind = $detectProofKind($proofUrl ?? $latestHeaderProofPath);
                            @endphp
                            <button
                                type="button"
                                class="bill-proof-link"
                                data-guest-proof-open
                                data-proof-url="{{ $proofUrl }}"
                                data-proof-kind="{{ $proofKind }}"
                                data-proof-title="{{ $paymentName }} — Bukti Pembayaran"
                            >📎 Lihat Bukti</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

        </div>
    </div>
</div>

{{-- Modal detail tagihan (snapshot + cicilan) --}}
<div id="guest-detail-modal" class="modal-overlay" hidden aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="guest-detail-title">
        <div class="modal-header">
            <div>
                <div class="modal-eyebrow">Tagihan</div>
                <h2 id="guest-detail-title" class="modal-title">Detail Tagihan</h2>
                <div id="guest-detail-subtitle" style="margin-top: 4px; color: var(--gray); font-weight: 700; font-size: 13px;"></div>
            </div>

            <button type="button" class="modal-close" data-guest-detail-close aria-label="Tutup">✕</button>
        </div>

        <div class="modal-body" style="padding: 18px 20px;">
            <div id="guest-detail-content" class="bill-detail-grid"></div>

            <div style="display:flex; gap: 10px; flex-wrap: wrap; margin-top: 12px;">
                <button type="button" class="btn-primary" data-guest-detail-close>Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal preview bukti pembayaran (image/pdf) --}}
<div id="guest-proof-modal" class="modal-overlay" hidden aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="guest-proof-title">
        <div class="modal-header">
            <div>
                <div class="modal-eyebrow">Bukti Pembayaran</div>
                <h2 id="guest-proof-title" class="modal-title">Lihat Bukti</h2>
            </div>

            <button type="button" class="modal-close" data-guest-proof-close aria-label="Tutup">✕</button>
        </div>

        <div class="modal-body" style="padding: 18px 20px;">
            <div class="bill-proof-preview">
                <iframe id="guest-proof-iframe" title="Preview PDF" hidden></iframe>
                <img id="guest-proof-img" alt="Bukti Pembayaran" hidden />
            </div>

            <div style="display:flex; gap: 10px; flex-wrap: wrap; margin-top: 12px;">
                <a id="guest-proof-download" class="btn-secondary" href="#" download>⬇️ Unduh</a>
                <button type="button" class="btn-primary" data-guest-proof-close>Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal upload bukti pembayaran (reusable) --}}
<div id="guest-pay-modal" class="modal-overlay" hidden aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="guest-pay-title">
        <div class="modal-header">
            <div>
                <div class="modal-eyebrow">Pembayaran</div>
                <h2 id="guest-pay-title" class="modal-title">Upload Bukti Pembayaran</h2>
                <div id="guest-pay-subtitle" style="margin-top: 4px; color: var(--gray); font-weight: 700; font-size: 13px;"></div>
            </div>

            <button type="button" class="modal-close" data-guest-pay-close aria-label="Tutup">✕</button>
        </div>

        <div class="modal-body" style="padding: 18px 20px;">
            <form id="guest-pay-form" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="guest-payment-method">Metode Pembayaran</label>
                    <select id="guest-payment-method" name="payment_method" class="form-input" required>
                        <option value="" selected disabled>Pilih metode</option>
                        <option value="transfer_bank">Transfer Bank</option>
                        <option value="e_wallet">E-Wallet</option>
                        <option value="cash">Cash</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                <div id="guest-payment-method-item-group" class="form-group" hidden>
                    <label id="guest-payment-method-item-label" class="form-label" for="guest-payment-method-item">Pilih Tujuan</label>
                    <select id="guest-payment-method-item" name="payment_method_item_id" class="form-input">
                        <option value="" selected disabled>Pilih</option>
                    </select>
                    <div class="form-label-hint" id="guest-payment-method-item-hint">Pilih rekening / akun tujuan agar admin bisa memverifikasi lebih cepat.</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="guest-proof-file">Upload Bukti Pembayaran</label>
                    <input id="guest-proof-file" type="file" name="proof_file" class="form-input" accept="image/*,application/pdf" required />
                    <div class="form-label-hint">Maksimal 4MB. Format disarankan: JPG/PNG/PDF.</div>
                </div>

                <div style="display:flex; gap: 10px; flex-wrap: wrap; margin-top: 12px;">
                    <button type="submit" class="btn-primary">Kirim Bukti</button>
                    <button type="button" class="btn-secondary" data-guest-pay-close>Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        const modal = document.getElementById('guest-pay-modal');
        const form = document.getElementById('guest-pay-form');
        const titleEl = document.getElementById('guest-pay-title');
        const subtitleEl = document.getElementById('guest-pay-subtitle');

        const paymentMethodSelect = document.getElementById('guest-payment-method');
        const methodItemGroup = document.getElementById('guest-payment-method-item-group');
        const methodItemLabel = document.getElementById('guest-payment-method-item-label');
        const methodItemSelect = document.getElementById('guest-payment-method-item');
        const methodItemHint = document.getElementById('guest-payment-method-item-hint');

        const proofModal = document.getElementById('guest-proof-modal');
        const proofTitleEl = document.getElementById('guest-proof-title');
        const proofIframe = document.getElementById('guest-proof-iframe');
        const proofImg = document.getElementById('guest-proof-img');
        const proofDownload = document.getElementById('guest-proof-download');

        const detailModal = document.getElementById('guest-detail-modal');
        const detailTitleEl = document.getElementById('guest-detail-title');
        const detailSubtitleEl = document.getElementById('guest-detail-subtitle');
        const detailContentEl = document.getElementById('guest-detail-content');

        function openModal(payUrl, titleText, subtitleText) {
            if (!modal || !form) return;
            form.setAttribute('action', payUrl || '');
            if (titleEl) titleEl.textContent = titleText || 'Upload Bukti Pembayaran';
            if (subtitleEl) subtitleEl.textContent = subtitleText || '';

            // Reset method selects
            if (paymentMethodSelect) paymentMethodSelect.value = '';
            if (methodItemSelect) methodItemSelect.innerHTML = '<option value="" selected disabled>Pilih</option>';
            if (methodItemSelect) methodItemSelect.required = false;
            if (methodItemGroup) methodItemGroup.hidden = true;

            modal.hidden = false;
            modal.setAttribute('aria-hidden', 'false');
        }

        function closeModal() {
            if (!modal) return;
            modal.hidden = true;
            modal.setAttribute('aria-hidden', 'true');
        }

        const bankOptions = @json(collect($paymentMethods ?? [])->where('type', 'bank')->map(fn($m) => [
            'id' => $m->id,
            'label' => (string)($m->label ?? ''),
            'account_number' => (string)($m->account_number ?? ''),
            'account_name' => (string)($m->account_name ?? ''),
        ])->values()->all());

        const ewalletOptions = @json(collect($paymentMethods ?? [])->where('type', 'ewallet')->map(fn($m) => [
            'id' => $m->id,
            'label' => (string)($m->label ?? ''),
            'account_number' => (string)($m->account_number ?? ''),
            'account_name' => (string)($m->account_name ?? ''),
        ])->values()->all());

        function renderMethodItemOptions(kind) {
            if (!methodItemGroup || !methodItemSelect) return;

            const isBank = kind === 'transfer_bank';
            const isEwallet = kind === 'e_wallet';
            const list = isBank ? (bankOptions || []) : (isEwallet ? (ewalletOptions || []) : []);

            if (!isBank && !isEwallet) {
                methodItemGroup.hidden = true;
                methodItemSelect.required = false;
                methodItemSelect.innerHTML = '<option value="" selected disabled>Pilih</option>';
                return;
            }

            // Only show if we have configured items.
            if (!Array.isArray(list) || list.length === 0) {
                methodItemGroup.hidden = true;
                methodItemSelect.required = false;
                methodItemSelect.innerHTML = '<option value="" selected disabled>Pilih</option>';
                return;
            }

            methodItemGroup.hidden = false;
            methodItemSelect.required = true;
            if (methodItemLabel) {
                methodItemLabel.textContent = isBank ? 'Pilih Bank Tujuan' : 'Pilih Provider E-Wallet';
            }
            if (methodItemHint) {
                methodItemHint.textContent = isBank
                    ? 'Pilih rekening bank tujuan.'
                    : 'Pilih provider e-wallet tujuan.';
            }

            methodItemSelect.innerHTML = '<option value="" selected disabled>Pilih</option>';
            list.forEach((opt) => {
                const optionEl = document.createElement('option');
                optionEl.value = String(opt.id || '');
                const left = (opt.label || '').trim() || '-';
                const acc = (opt.account_number || '').trim();
                const name = (opt.account_name || '').trim();
                const extraParts = [];
                if (acc) extraParts.push(acc);
                if (name) extraParts.push('a.n ' + name);
                optionEl.textContent = extraParts.length ? (left + ' — ' + extraParts.join(' • ')) : left;
                methodItemSelect.appendChild(optionEl);
            });
        }

        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', function () {
                renderMethodItemOptions(paymentMethodSelect.value);
            });
        }

        function openProofModal(proofUrl, titleText) {
            if (!proofModal) return;

            const safeUrl = proofUrl || '';
            const safeTitle = titleText || 'Lihat Bukti';
            if (proofTitleEl) proofTitleEl.textContent = safeTitle;

            const kindFromData = (this && this.getAttribute) ? this.getAttribute('data-proof-kind') : null;
            const cleanUrl = safeUrl.toLowerCase().split('?')[0].split('#')[0];
            const isPdf = (kindFromData === 'pdf') || cleanUrl.endsWith('.pdf');

            if (proofIframe) {
                proofIframe.hidden = !isPdf;
                proofIframe.setAttribute('src', isPdf ? safeUrl : '');
            }

            if (proofImg) {
                proofImg.hidden = isPdf;
                proofImg.setAttribute('src', !isPdf ? safeUrl : '');
            }

            if (proofDownload) {
                proofDownload.setAttribute('href', safeUrl || '#');
                proofDownload.style.pointerEvents = safeUrl ? 'auto' : 'none';
                proofDownload.style.opacity = safeUrl ? '1' : '0.6';
            }

            proofModal.hidden = false;
            proofModal.setAttribute('aria-hidden', 'false');
        }

        function closeProofModal() {
            if (!proofModal) return;
            proofModal.hidden = true;
            proofModal.setAttribute('aria-hidden', 'true');

            if (proofIframe) proofIframe.setAttribute('src', '');
            if (proofImg) proofImg.setAttribute('src', '');
        }

        function closeDetailModal() {
            if (!detailModal) return;
            detailModal.hidden = true;
            detailModal.setAttribute('aria-hidden', 'true');
            if (detailContentEl) detailContentEl.innerHTML = '';
        }

        function openDetailModal(detail) {
            if (!detailModal || !detailContentEl) return;
            const safe = detail || {};

            if (detailTitleEl) detailTitleEl.textContent = (safe.payment_name || 'Detail Tagihan');
            if (detailSubtitleEl) {
                const parts = [];
                if (safe.period) parts.push('Periode ' + safe.period);
                if (safe.status_label) parts.push(safe.status_label);
                if (safe.final_amount_label) parts.push(safe.final_amount_label);
                detailSubtitleEl.textContent = parts.join(' • ');
            }

            detailContentEl.innerHTML = '';

            // Ringkasan
            const summary = document.createElement('div');
            summary.className = 'bill-detail-section';
            const summaryTitle = document.createElement('div');
            summaryTitle.className = 'bill-detail-title';
            summaryTitle.textContent = 'Ringkasan';
            summary.appendChild(summaryTitle);

            if (safe.proof_url) {
                const proofBtnWrap = document.createElement('div');
                proofBtnWrap.style.marginBottom = '10px';

                const proofBtn = document.createElement('button');
                proofBtn.type = 'button';
                proofBtn.className = 'bill-proof-link';
                proofBtn.textContent = '📎 Lihat Bukti';
                proofBtn.setAttribute('data-proof-kind', safe.proof_kind || 'image');
                proofBtn.addEventListener('click', () => {
                    openProofModal.call(proofBtn, safe.proof_url, (safe.payment_name || 'Tagihan') + ' — Bukti Pembayaran');
                });

                proofBtnWrap.appendChild(proofBtn);
                summary.appendChild(proofBtnWrap);
            }

            const summaryTable = document.createElement('table');
            summaryTable.className = 'bill-detail-table';
            const summaryRows = [
                ['Status', safe.status_label || '-'],
                ['Metode', safe.payment_method_label || '-'],
                ['Dibayar', safe.paid_at_label || '-'],
                ['Total', safe.total_amount_label || '-'],
                ['Diskon', safe.discount_amount_label || '-'],
                ['Total Akhir', safe.final_amount_label || '-'],
            ];
            summaryRows.forEach(([k, v]) => {
                const tr = document.createElement('tr');
                const td1 = document.createElement('td');
                td1.textContent = k;
                const td2 = document.createElement('td');
                td2.textContent = v;
                tr.appendChild(td1);
                tr.appendChild(td2);
                summaryTable.appendChild(tr);
            });
            summary.appendChild(summaryTable);
            detailContentEl.appendChild(summary);

            // Rincian biaya (snapshot)
            const snapshot = Array.isArray(safe.snapshot) ? safe.snapshot : [];
            if (snapshot.length > 0) {
                const fees = document.createElement('div');
                fees.className = 'bill-detail-section';
                const feesTitle = document.createElement('div');
                feesTitle.className = 'bill-detail-title';
                feesTitle.textContent = 'Rincian Biaya';
                fees.appendChild(feesTitle);

                const feesTable = document.createElement('table');
                feesTable.className = 'bill-detail-table';
                snapshot.forEach((row) => {
                    const label = (row && typeof row.label === 'string' ? row.label : 'Komponen');
                    const qty = (row && Number.isFinite(Number(row.qty)) ? Number(row.qty) : 1);
                    const amount = (row && Number.isFinite(Number(row.amount)) ? Number(row.amount) : 0);

                    const left = qty && qty > 1 ? `${label} (x${qty})` : label;
                    const right = new Intl.NumberFormat('id-ID').format(amount * (qty || 1));

                    const tr = document.createElement('tr');
                    const td1 = document.createElement('td');
                    td1.textContent = left;
                    const td2 = document.createElement('td');
                    td2.textContent = 'Rp ' + right;
                    tr.appendChild(td1);
                    tr.appendChild(td2);
                    feesTable.appendChild(tr);
                });

                fees.appendChild(feesTable);
                detailContentEl.appendChild(fees);
            }

            // Riwayat bukti (header)
            const proofHistory = Array.isArray(safe.proof_history) ? safe.proof_history : [];
            if (proofHistory.length > 0) {
                const hist = document.createElement('div');
                hist.className = 'bill-detail-section';
                const histTitle = document.createElement('div');
                histTitle.className = 'bill-detail-title';
                histTitle.textContent = 'Riwayat Bukti Pembayaran';
                hist.appendChild(histTitle);

                const histTable = document.createElement('table');
                histTable.className = 'bill-detail-table';

                proofHistory.forEach((row, idx) => {
                    const tr = document.createElement('tr');

                    const left = document.createElement('td');
                    const attempt = 'Bukti #' + (idx + 1);
                    const when = row.uploaded_at ? (' • ' + row.uploaded_at) : '';
                    left.textContent = attempt + when;

                    const right = document.createElement('td');
                    const statusLabel = (row.status === 'approved')
                        ? 'Disetujui'
                        : (row.status === 'rejected')
                            ? 'Ditolak'
                            : 'Menunggu';
                    right.textContent = (row.payment_method || '-') + ' • ' + statusLabel;

                    tr.appendChild(left);
                    tr.appendChild(right);
                    histTable.appendChild(tr);

                    if (row.admin_note) {
                        const noteTr = document.createElement('tr');
                        const noteTd1 = document.createElement('td');
                        noteTd1.textContent = 'Catatan admin';
                        const noteTd2 = document.createElement('td');
                        noteTd2.textContent = row.admin_note;
                        noteTr.appendChild(noteTd1);
                        noteTr.appendChild(noteTd2);
                        histTable.appendChild(noteTr);
                    }

                    if (row.proof_url) {
                        const viewTr = document.createElement('tr');
                        const viewTd1 = document.createElement('td');
                        viewTd1.textContent = 'File';
                        const viewTd2 = document.createElement('td');
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'bill-proof-link';
                        btn.textContent = '📎 Lihat Bukti';
                        btn.setAttribute('data-proof-kind', row.proof_kind || 'image');
                        btn.addEventListener('click', () => {
                            openProofModal.call(btn, row.proof_url, (safe.payment_name || 'Tagihan') + ' — ' + attempt);
                        });
                        viewTd2.appendChild(btn);
                        viewTr.appendChild(viewTd1);
                        viewTr.appendChild(viewTd2);
                        histTable.appendChild(viewTr);
                    }
                });

                hist.appendChild(histTable);
                detailContentEl.appendChild(hist);
            }

            // Cicilan (riwayat sederhana)
            const installments = Array.isArray(safe.installments) ? safe.installments : [];
            if (installments.length > 0) {
                const inst = document.createElement('div');
                inst.className = 'bill-detail-section';
                const instTitle = document.createElement('div');
                instTitle.className = 'bill-detail-title';
                instTitle.textContent = 'Cicilan';
                inst.appendChild(instTitle);

                const list = document.createElement('div');
                list.className = 'bill-detail-installments';

                installments.forEach((row) => {
                    const card = document.createElement('div');
                    card.className = 'bill-detail-installment';

                    const top = document.createElement('div');
                    top.className = 'bill-detail-installment-top';

                    const name = document.createElement('div');
                    name.className = 'bill-detail-installment-name';
                    name.textContent = 'Cicilan #' + (row.number ?? '-');
                    top.appendChild(name);

                    if (row.proof_url) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'bill-proof-link';
                        btn.textContent = '📎 Lihat Bukti';
                        btn.setAttribute('data-proof-kind', row.proof_kind || 'image');
                        btn.addEventListener('click', () => {
                            openProofModal.call(btn, row.proof_url, (safe.payment_name || 'Tagihan') + ' — Bukti Cicilan #' + (row.number ?? '-'));
                        });
                        top.appendChild(btn);
                    }

                    card.appendChild(top);

                    const meta = document.createElement('div');
                    meta.className = 'bill-detail-installment-meta';

                    const metaRows = [
                        ['Jatuh tempo', row.due_date || '-'],
                        ['Nominal', row.amount_label || '-'],
                        ['Status', (row.status === 'paid' ? 'Lunas' : (row.status === 'failed' ? 'Gagal' : 'Menunggu'))],
                        ['Metode', row.payment_method || '-'],
                        ['Dibayar', row.paid_at || '-'],
                    ];

                    metaRows.forEach(([k, v]) => {
                        const r = document.createElement('div');
                        r.className = 'bill-detail-installment-meta-row';
                        const s = document.createElement('span');
                        s.textContent = k;
                        const st = document.createElement('strong');
                        st.textContent = v;
                        r.appendChild(s);
                        r.appendChild(st);
                        meta.appendChild(r);
                    });

                    card.appendChild(meta);

                    const instHistory = Array.isArray(row.proof_history) ? row.proof_history : [];
                    if (instHistory.length > 0) {
                        const histWrap = document.createElement('div');
                        histWrap.style.marginTop = '10px';

                        const histTitle = document.createElement('div');
                        histTitle.style.fontSize = '12px';
                        histTitle.style.fontWeight = '900';
                        histTitle.style.color = 'var(--gray)';
                        histTitle.style.textTransform = 'uppercase';
                        histTitle.style.letterSpacing = '0.06em';
                        histTitle.textContent = 'Riwayat Bukti';
                        histWrap.appendChild(histTitle);

                        instHistory.forEach((h, idx) => {
                            const line = document.createElement('div');
                            line.style.display = 'flex';
                            line.style.justifyContent = 'space-between';
                            line.style.gap = '10px';
                            line.style.marginTop = '8px';
                            line.style.fontSize = '12px';

                            const left = document.createElement('div');
                            left.style.color = 'var(--gray)';
                            left.style.fontWeight = '800';
                            left.textContent = 'Bukti #' + (idx + 1) + (h.uploaded_at ? (' • ' + h.uploaded_at) : '');

                            const right = document.createElement('div');
                            right.style.display = 'flex';
                            right.style.alignItems = 'center';
                            right.style.gap = '10px';
                            right.style.justifyContent = 'flex-end';

                            const statusLabel = (h.status === 'approved')
                                ? 'Disetujui'
                                : (h.status === 'rejected')
                                    ? 'Ditolak'
                                    : 'Menunggu';

                            const status = document.createElement('strong');
                            status.style.color = 'var(--dark)';
                            status.textContent = (h.payment_method || '-') + ' • ' + statusLabel;
                            right.appendChild(status);

                            if (h.proof_url) {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'bill-proof-link';
                                btn.textContent = '📎';
                                btn.setAttribute('data-proof-kind', h.proof_kind || 'image');
                                btn.addEventListener('click', () => {
                                    openProofModal.call(btn, h.proof_url, (safe.payment_name || 'Tagihan') + ' — Bukti Cicilan #' + (row.number ?? '-') + ' #' + (idx + 1));
                                });
                                right.appendChild(btn);
                            }

                            line.appendChild(left);
                            line.appendChild(right);
                            histWrap.appendChild(line);

                            if (h.admin_note) {
                                const note = document.createElement('div');
                                note.style.marginTop = '6px';
                                note.style.fontSize = '12px';
                                note.style.color = 'var(--dark)';
                                note.style.fontWeight = '800';
                                note.textContent = 'Catatan admin: ' + h.admin_note;
                                histWrap.appendChild(note);
                            }
                        });

                        card.appendChild(histWrap);
                    }
                    list.appendChild(card);
                });

                inst.appendChild(list);
                detailContentEl.appendChild(inst);
            }

            // Instruksi
            const help = document.createElement('div');
            help.className = 'bill-detail-section';
            const helpTitle = document.createElement('div');
            helpTitle.className = 'bill-detail-title';
            helpTitle.textContent = 'Instruksi Pembayaran';
            help.appendChild(helpTitle);

            const helpText = document.createElement('div');
            helpText.style.fontSize = '12px';
            helpText.style.fontWeight = '800';
            helpText.style.color = 'var(--dark)';
            helpText.style.lineHeight = '1.5';
            helpText.textContent = 'Silakan lakukan pembayaran sesuai metode yang dipilih, lalu upload bukti pembayaran. Status akan menjadi “Menunggu Verifikasi” sampai admin memverifikasi.';
            help.appendChild(helpText);
            detailContentEl.appendChild(help);

            detailModal.hidden = false;
            detailModal.setAttribute('aria-hidden', 'false');
        }

        document.querySelectorAll('[data-guest-pay-open]')?.forEach((btn) => {
            btn.addEventListener('click', () => {
                const payUrl = btn.getAttribute('data-pay-url');
                const titleText = btn.getAttribute('data-pay-title');
                const subtitleText = btn.getAttribute('data-pay-subtitle');
                openModal(payUrl, titleText, subtitleText);
            });
        });

        document.querySelectorAll('[data-guest-pay-close]')?.forEach((btn) => btn.addEventListener('click', closeModal));

        document.querySelectorAll('[data-guest-proof-open]')?.forEach((btn) => {
            btn.addEventListener('click', () => {
                const proofUrl = btn.getAttribute('data-proof-url');
                const titleText = btn.getAttribute('data-proof-title');
                openProofModal.call(btn, proofUrl, titleText);
            });
        });

        document.querySelectorAll('[data-guest-proof-close]')?.forEach((btn) => btn.addEventListener('click', closeProofModal));

        document.querySelectorAll('[data-guest-detail-open]')?.forEach((btn) => {
            btn.addEventListener('click', () => {
                const raw = btn.getAttribute('data-detail');
                let parsed = null;
                try {
                    parsed = raw ? JSON.parse(raw) : null;
                } catch (_) {
                    parsed = null;
                }
                openDetailModal(parsed);
            });
        });

        document.querySelectorAll('[data-guest-detail-close]')?.forEach((btn) => btn.addEventListener('click', closeDetailModal));

        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
        }

        if (proofModal) {
            proofModal.addEventListener('click', (e) => {
                if (e.target === proofModal) closeProofModal();
            });
        }

        if (detailModal) {
            detailModal.addEventListener('click', (e) => {
                if (e.target === detailModal) closeDetailModal();
            });
        }

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
                closeProofModal();
                closeDetailModal();
            }
        });
    })();
</script>
