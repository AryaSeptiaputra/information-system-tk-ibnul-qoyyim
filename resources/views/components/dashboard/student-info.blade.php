<style>
    .student-info {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border: 2px solid var(--green-light);
        margin-bottom: 32px;
    }

    .student-info h3 {
        font-family: 'Fredoka One', cursive;
        font-size: 18px;
        color: var(--dark);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .student-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .student-detail-item {
        background: var(--bg);
        border-radius: 12px;
        padding: 14px;
        border-left: 3px solid var(--green);
    }

    .student-detail-label {
        font-size: 12px;
        font-weight: 800;
        color: var(--gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .student-detail-value {
        font-size: 15px;
        font-weight: 700;
        color: var(--dark);
    }

    @media (max-width: 600px) {
        .student-info {
            padding: 16px;
        }

        .student-details {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="student-info">
    <h3>👦 Informasi Siswa</h3>
    <div class="student-details">
        <div class="student-detail-item">
            <div class="student-detail-label">Nama Siswa</div>
            <div class="student-detail-value">{{ $studentInfo['name'] ?? '-' }}</div>
        </div>
        <div class="student-detail-item">
            <div class="student-detail-label">Jenis Kelamin</div>
            <div class="student-detail-value">
                @if(($studentInfo['gender'] ?? null) === 'male')
                    Laki-laki
                @elseif(($studentInfo['gender'] ?? null) === 'female')
                    Perempuan
                @else
                    -
                @endif
            </div>
        </div>
        <div class="student-detail-item">
            <div class="student-detail-label">Tanggal Lahir</div>
            <div class="student-detail-value">
                @if($studentInfo['birth_date'] ?? null)
                    {{ \Carbon\Carbon::parse($studentInfo['birth_date'])->format('d M Y') }}
                @else
                    -
                @endif
            </div>
        </div>
        <div class="student-detail-item">
            <div class="student-detail-label">Kelompok</div>
            <div class="student-detail-value">
                @if(($studentInfo['group'] ?? null) === 'A')
                    Kelompok A (4-5 Tahun)
                @elseif(($studentInfo['group'] ?? null) === 'B')
                    Kelompok B (5-6 Tahun)
                @else
                    {{ $studentInfo['group'] ?? '-' }}
                @endif
            </div>
        </div>
        <div class="student-detail-item">
            <div class="student-detail-label">Status Siswa</div>
            <div class="student-detail-value">
                @if(($studentInfo['status'] ?? null) === 'active')
                    Aktif
                @elseif(($studentInfo['status'] ?? null) === 'inactive')
                    Tidak Aktif
                @else
                    {{ $studentInfo['status'] ?? '-' }}
                @endif
            </div>
        </div>
    </div>
</div>
