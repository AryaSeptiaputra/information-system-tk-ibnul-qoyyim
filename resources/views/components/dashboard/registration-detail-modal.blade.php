@php
    /** @var \App\Models\Registration|null $registration */
    $candidate = $registration?->candidate_data ?? [];
    $parents = $registration?->parents_data ?? [];
@endphp

<div id="registration-detail-modal" class="modal-overlay" hidden aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="registration-detail-title">
        <div class="modal-header">
            <div>
                <div class="modal-eyebrow">Detail Pendaftaran</div>
                <h2 id="registration-detail-title" class="modal-title">
                    Status: {{ strtoupper(str_replace('_', ' ', $registration->status ?? '-')) }}
                </h2>
            </div>

            <button type="button" class="modal-close" data-modal-close="registration-detail" aria-label="Tutup">
                ✕
            </button>
        </div>

        <div class="modal-body">
            @if(($registration->status ?? null) === 'pending')
                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        <h3>Data Calon Siswa</h3>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $candidate['name'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>TTL</span><strong>{{ $candidate['birth_place'] ?? '-' }}, {{ $candidate['birth_date'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Gender</span><strong>
                            @php($gender = $candidate['gender'] ?? null)
                            {{ $gender === 'pria' ? 'Laki-laki' : ($gender === 'perempuan' ? 'Perempuan' : '-') }}
                        </strong></div>
                        <div class="registration-detail-row"><span>Kelompok</span><strong>{{ $registration->group ?? '-' }}</strong></div>
                    </div>

                    <div class="registration-detail-block">
                        <h3>Data Ayah/Wali</h3>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $parents['father_name'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nomor Telepon</span><strong>{{ $parents['father_phone'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Pekerjaan</span><strong>{{ $parents['father_job'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Alamat</span><strong>{{ $parents['father_address'] ?? '-' }}</strong></div>
                    </div>

                    <div class="registration-detail-block">
                        <h3>Data Ibu</h3>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $parents['mother_name'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nomor Telepon</span><strong>{{ $parents['mother_phone'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Pekerjaan</span><strong>{{ $parents['mother_job'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Alamat</span><strong>{{ $parents['mother_address'] ?? '-' }}</strong></div>
                    </div>
                </div>
            @else
                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        <h3>Data Calon Siswa</h3>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $candidate['name'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>TTL</span><strong>{{ $candidate['birth_place'] ?? '-' }}, {{ $candidate['birth_date'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Gender</span><strong>{{ $candidate['gender'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Kelompok</span><strong>{{ $registration->group ?? '-' }}</strong></div>
                    </div>

                    <div class="registration-detail-block">
                        <h3>Data Ayah/Wali</h3>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $parents['father_name'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nomor Telepon</span><strong>{{ $parents['father_phone'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Pekerjaan</span><strong>{{ $parents['father_job'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Alamat</span><strong>{{ $parents['father_address'] ?? '-' }}</strong></div>
                    </div>

                    <div class="registration-detail-block">
                        <h3>Data Ibu</h3>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $parents['mother_name'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nomor Telepon</span><strong>{{ $parents['mother_phone'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Pekerjaan</span><strong>{{ $parents['mother_job'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Alamat</span><strong>{{ $parents['mother_address'] ?? '-' }}</strong></div>
                    </div>
                </div>

                <div class="registration-detail-divider"></div>

                <div class="registration-detail-block">
                    <h3>Deadline Pembayaran</h3>
                    <div class="registration-detail-row"><span>Payment deadline</span><strong>{{ $registration?->payment_deadline?->format('Y-m-d') ?? '-' }}</strong></div>
                    <div class="registration-detail-row"><span>Grace period until</span><strong>{{ $registration?->grace_period_until?->format('Y-m-d') ?? '-' }}</strong></div>
                    <div class="registration-detail-row"><span>Status deadline</span><strong>{{ $deadlineStatus['status'] ?? '-' }}</strong></div>
                </div>
            @endif

            @if(($registration->reject_reason ?? null))
                <div class="registration-detail-alert">
                    <strong>Alasan ditolak:</strong> {{ $registration->reject_reason }}
                </div>
            @endif
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-small secondary" data-modal-close="registration-detail">Tutup</button>
        </div>
    </div>
</div>
