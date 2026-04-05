<style>
    .registration-status {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .status-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border-left: 5px solid;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .status-card.approved {
        border-left-color: var(--green);
    }

    .status-card.pending {
        border-left-color: var(--yellow);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: white;
        padding: 8px 14px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 800;
        margin-bottom: 16px;
    }

    .status-badge.approved {
        background: #d4f5e3;
        color: var(--green-dark);
    }

    .status-badge.pending {
        background: #fff8e1;
        color: #e6c200;
    }

    .status-card h3 {
        font-family: 'Fredoka One', cursive;
        font-size: 18px;
        color: var(--dark);
        margin-bottom: 12px;
    }

    .status-card p {
        color: var(--gray);
        font-size: 14px;
        line-height: 1.6;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .status-card-action {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn-small {
        padding: 10px 18px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-small.primary {
        background: linear-gradient(135deg, var(--green), var(--green-dark));
        color: white;
        box-shadow: 0 4px 12px rgba(46,204,113,0.3);
    }

    .btn-small.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(46,204,113,0.4);
    }

    .btn-small.secondary {
        background: white;
        color: var(--green-dark);
        border: 2px solid var(--green);
    }

    .btn-small.secondary:hover {
        background: var(--green-light);
    }

    .empty-state {
        background: white;
        border-radius: 16px;
        padding: 40px;
        text-align: center;
        border: 2px dashed var(--green-light);
        color: var(--gray);
    }

    .empty-state h3 {
        font-family: 'Fredoka One', cursive;
        color: var(--dark);
        margin-bottom: 8px;
    }

    .empty-state p {
        font-size: 14px;
        margin-bottom: 16px;
    }

    .empty-state a {
        color: var(--green);
        font-weight: 800;
        text-decoration: none;
    }

    @media (max-width: 600px) {
        .registration-status {
            grid-template-columns: 1fr;
        }
    }
</style>

@if($pendingRegistration)
    <div class="registration-status">
        <div class="status-card pending">
            @if(($pendingRegistration->status ?? null) === 'rejected')
                <div class="status-badge pending">⚠️ Pendaftaran Ditolak</div>
                <h3>Status Pendaftaran</h3>
                <p>
                    Pendaftaran Anda ditolak oleh admin. Silakan lihat detail untuk alasan penolakan dan Anda dapat mengajukan pendaftaran ulang.
                </p>
            @else
                <div class="status-badge pending">⏳ Menunggu Persetujuan</div>
                <h3>Status Pendaftaran</h3>
                <p>
                    Pendaftaran Anda sedang diproses. Tim administrasi kami akan mereview data yang Anda kirimkan.
                    Biasanya proses ini memakan waktu 2-3 hari kerja.
                </p>
            @endif
            <div class="status-card-action">
                <button type="button" class="btn-small secondary" data-modal-open="registration-detail">📋 Lihat Detail</button>
                @if(($pendingRegistration->status ?? null) === 'rejected')
                    <a href="{{ route('dashboard') }}#registration-form" class="btn-small primary">📝 Daftar Ulang</a>
                @endif
            </div>
        </div>
    </div>
@elseif($approvedRegistration && $hasChild)
    <div class="registration-status">
        <div class="status-card approved">
            <div class="status-badge approved">✓ Pendaftaran Diterima</div>
            <h3>Selamat! Pendaftaran Diterima</h3>
            <p>
                Anak Anda telah diterima di TK Ibnul Qoyyim Sulawesi. Data siswa sudah tersimpan dalam sistem. 
                Silakan melengkapi kewajibannya dan hadiri pertemuan orang tua.
            </p>
            <div class="status-card-action">
                <button type="button" class="btn-small secondary" data-modal-open="registration-detail">📋 Lihat Detail</button>
                @if(($studentInfo['id_student'] ?? null))
                    <a href="{{ route('payment.create', $studentInfo['id_student']) }}" class="btn-small primary">💳 Bayar Pendaftaran</a>
                @endif
            </div>
        </div>
    </div>
@else
    <div class="empty-state">
        <h3>📭 Belum Ada Pendaftaran</h3>
        <p>Anda belum memiliki pendaftaran anak. Silakan mulai pendaftaran melalui tombol di bawah.</p>
        <p>
            <a href="{{ route('dashboard') }}#registration-form">Daftar Anak Sekarang</a>
        </p>
    </div>
@endif
