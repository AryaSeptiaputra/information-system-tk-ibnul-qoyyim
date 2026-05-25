@php
    /** @var \App\Models\Registration|null $registration */
    $candidate = $registration?->candidate_data ?? [];
    $parents = $registration?->parents_data ?? [];

    $candidateBirthDateRaw = $candidate['birth_date'] ?? null;
    $candidateBirthDateLabel = '-';
    if ($candidateBirthDateRaw) {
        if (is_string($candidateBirthDateRaw)) {
            $candidateBirthDateLabel = trim(explode(' ', $candidateBirthDateRaw)[0]);
        } elseif (is_object($candidateBirthDateRaw) && method_exists($candidateBirthDateRaw, 'format')) {
            $candidateBirthDateLabel = $candidateBirthDateRaw->format('Y-m-d');
        } else {
            $candidateBirthDateLabel = (string)$candidateBirthDateRaw;
        }
    }
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
                        <div class="registration-detail-row"><span>TTL</span><strong>{{ $candidate['birth_place'] ?? '-' }}, {{ $candidateBirthDateLabel }}</strong></div>
                        <div class="registration-detail-row"><span>Gender</span><strong>
                            @php
                                $gender = $candidate['gender'] ?? null;
                            @endphp
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
                        <div class="registration-detail-row"><span>TTL</span><strong>{{ $candidate['birth_place'] ?? '-' }}, {{ $candidateBirthDateLabel }}</strong></div>
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
            @endif

            @php
                $guestRegistrationDocs = [
                    ['label' => 'Kartu Keluarga', 'path' => $registration?->kk_file_path, 'alt' => 'Kartu Keluarga'],
                    ['label' => 'Pas Foto Anak', 'path' => $registration?->photo_file_path, 'alt' => 'Pas Foto Anak'],
                    ['label' => 'Akta Kelahiran', 'path' => $registration?->birth_certificate_file_path, 'alt' => 'Akta Kelahiran'],
                ];
                $guestHasAnyDoc = collect($guestRegistrationDocs)->contains(fn ($d) => !empty($d['path']));
            @endphp

            @if($guestHasAnyDoc)
                <div class="registration-detail-divider"></div>
                <div class="registration-detail-block">
                    <h3>Dokumen yang Diunggah</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-top: 12px;">
                        @foreach($guestRegistrationDocs as $doc)
                            @php
                                $docPath = $doc['path'];
                                $docExt = $docPath ? strtolower(pathinfo($docPath, PATHINFO_EXTENSION)) : '';
                                $docIsImg = in_array($docExt, ['jpg','jpeg','png','webp','gif']);
                            @endphp
                            <div style="border:1px solid #e5e7eb;border-radius:8px;padding:10px;background:#fafafa;">
                                <div style="font-weight:700;margin-bottom:6px;font-size:13px;">{{ $doc['label'] }}</div>
                                @if($docPath)
                                    @if($docIsImg)
                                        <a href="{{ asset($docPath) }}" target="_blank" rel="noopener" title="Buka di jendela baru">
                                            <img src="{{ asset($docPath) }}" alt="{{ $doc['alt'] }}"
                                                 style="width:100%;max-height:140px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;display:block;">
                                        </a>
                                        <a href="{{ asset($docPath) }}" target="_blank" rel="noopener"
                                           style="display:inline-block;margin-top:6px;font-size:12px;color:#2563eb;">
                                            🔍 Buka di jendela baru
                                        </a>
                                    @else
                                        <a href="{{ asset($docPath) }}" target="_blank" rel="noopener"
                                           style="display:inline-block;padding:8px 12px;background:#fff;border:1px solid #e5e7eb;border-radius:6px;color:#1f2937;text-decoration:none;font-weight:600;font-size:13px;">
                                            📄 Lihat PDF
                                        </a>
                                    @endif
                                @else
                                    <div style="color:#9ca3af;font-size:12px;">Belum diunggah</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
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
