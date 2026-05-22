# User Flow — Sistem Bendahara Sekolah

> Dokumen ini menjelaskan **alur navigasi** dan **logic bisnis** dari sisi bendahara.
> Untuk detail tampilan UI per halaman, lihat dokumen `wireframe-spec-bendahara.md`.

---

## 1. Aktor

| Aktor | Peran |
|---|---|
| **Bendahara** | Mengelola dana masuk, melakukan pembayaran honor ke guru & kepala sekolah |

---

## 2. Alur Utama (High-Level)

```mermaid
flowchart TD
    A([Login Bendahara]) --> B[Dashboard Bendahara]
    B --> C{Pilih Aksi}
    C -->|Lihat dana / input dana masuk| D[Pop-up Input Dana]
    C -->|Bayar honor| E[Honor Page]
    D -->|Submit| B
    E --> F[Detail Guru / Kepsek]
    F --> G{Gaji sudah terisi?}
    G -->|Belum| H[Isi Gaji Manual]
    G -->|Sudah| I[Bayar Sekarang]
    H --> I
    I --> J([Pembayaran Tercatat])
    J --> B
```

---

## 3. Alur Detail per Fitur

### 3.1 Input Dana Masuk

```mermaid
flowchart LR
    A[Dashboard] --> B[Klik tombol Input Dana]
    B --> C[Pop-up muncul]
    C --> D[Isi Nominal]
    D --> E[Upload File bukti]
    E --> F[Klik SUBMIT]
    F --> G[Total dana ter-update di dashboard]
```

**Catatan:**
- Sumber dana dicatat berdasarkan kategori (SPP, BOP, BOSDA, HONDA)
- File bukti wajib (kuitansi/transfer)

---

### 3.2 Pembayaran Honor

```mermaid
flowchart TD
    A[Honor Page] --> B[Lihat list: Kepsek, Guru A, Guru B, ...]
    B --> C[Klik Bayar pada salah satu nama]
    C --> D[Halaman Detail Penerima]
    D --> E{Cek field gaji}
    E -->|Sudah terisi otomatis| F[Review: Pokok, Tunjangan, Potongan, Total]
    E -->|Belum terisi| G[Klik 'Isi Gaji' per komponen]
    G --> H[Pop-up Nominal]
    H --> I[Submit nominal]
    I --> F
    F --> J[Klik 'Bayar Sekarang']
    J --> K[Sistem kurangi sumber dana sesuai aturan]
    K --> L([Selesai])
```

---

## 4. Logic Bisnis — Sumber Dana

Aturan sumber dana untuk pembayaran honor:

```mermaid
flowchart TD
    A[Penerima Honor] --> B{Siapa?}
    B -->|Guru| C[Sumber: SPP + BOP + BOSDA]
    B -->|Kepala Sekolah| D[Sumber: HONDA / BOSDA]
    B -->|Wali Kelas| E[Perlakuan khusus*]
    C --> F[Potong saldo sumber]
    D --> F
    E --> F
```

> **\*Wali Kelas:** dari sketsa, ada catatan khusus untuk wali kelas tapi detail belum jelas.
> **TODO:** klarifikasi apakah wali kelas dapat tambahan dari sumber lain atau ada tunjangan terpisah.

---

## 5. Komponen Gaji

| Komponen | Sifat | Contoh |
|---|---|---|
| Gaji Pokok | Tambah (+) | Rp 100.000 |
| Tunjangan | Tambah (+) | Rp 40.000 |
| Potongan | Kurang (−) | Rp 20.000 |
| **Total** | Hasil akhir | **Rp 120.000** |

Rumus: `Total = Gaji Pokok + Tunjangan − Potongan`

---

## 6. State Halaman Detail

```mermaid
stateDiagram-v2
    [*] --> Kosong: Buka detail pertama kali
    Kosong --> Terisi: Isi semua komponen gaji
    Terisi --> Dibayar: Klik 'Bayar Sekarang'
    Dibayar --> [*]
    Terisi --> Terisi: Edit komponen gaji
```

---

## 7. Open Questions / TODO

- [ ] Detail perlakuan khusus untuk wali kelas (sumber dana tambahan? tunjangan ekstra?)
- [ ] Apakah field gaji bisa di-edit ulang setelah pembayaran?
- [ ] Validasi: bagaimana jika saldo sumber dana tidak mencukupi?
- [ ] Apakah ada riwayat / history pembayaran?
- [ ] Role selain bendahara (guru/kepsek apakah punya halaman sendiri?)
