# Wireframe Spec — Sistem Bendahara Sekolah

> Dokumen ini berisi **spesifikasi UI per halaman** (struktur, komponen, field).
> Untuk alur navigasi & logic bisnis, lihat `user-flow-bendahara.md`.

---

## Daftar Halaman

1. Dashboard Bendahara
2. Honor Page (List Penerima)
3. Detail Penerima Honor
4. Pop-up Input Dana
5. Pop-up Isi Gaji

---

## 1. Dashboard Bendahara

**Tujuan:** Halaman utama bendahara — ringkasan dana & akses ke fitur lain.

```
┌───────────────────────────────────────────────────────────┐
│ NAVBAR  [Logo]  Dashboard | Honor | Laporan | Profil      │
├───────────────────────────────────────────────────────────┤
│                                                           │
│  Ringkasan Dana                                           │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐          │
│  │  SPP    │ │  BOP    │ │ BOSDA   │ │ HONDA   │          │
│  │ Rp xxx  │ │ Rp xxx  │ │ Rp xxx  │ │ Rp xxx  │          │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘          │
│                                                           │
│  Total Dana: Rp xxx.xxx                                   │
│                                                           │
│  Riwayat Pendapatan                  [+ Input Dana]       │
│  ─────────────────────────────────                        │
│  • Sumber SPP — Rp 130.000 — 22 Mei                       │
│  • Sumber BOP — Rp 200.000 — 20 Mei                       │
│  • ...                                                    │
│                                                           │
└───────────────────────────────────────────────────────────┘
```

### Komponen
| Komponen | Tipe | Catatan |
|---|---|---|
| Navbar | Header | Konsisten di semua halaman |
| Card sumber dana | Card × 4 | SPP, BOP, BOSDA, HONDA |
| Total Dana | Text besar | Akumulasi semua sumber |
| List Riwayat Pendapatan | Table/List | Sortable by tanggal |
| Tombol "Input Dana" | Button (primary) | Membuka pop-up input dana |

---

## 2. Honor Page (List Penerima)

**Tujuan:** Daftar penerima honor yang siap dibayar.

```
┌───────────────────────────────────────────────────────────┐
│ NAVBAR                                                    │
├───────────────────────────────────────────────────────────┤
│                                                           │
│  Pembayaran Honor                                         │
│  ─────────────────────────────────                        │
│                                                           │
│  ┌─────────────────────────────────────────────┐          │
│  │ Kepala Sekolah          [Bayar]             │          │
│  ├─────────────────────────────────────────────┤          │
│  │ Guru A                  [Bayar]             │          │
│  ├─────────────────────────────────────────────┤          │
│  │ Guru B                  [Bayar]             │          │
│  ├─────────────────────────────────────────────┤          │
│  │ ...                                         │          │
│  └─────────────────────────────────────────────┘          │
│                                                           │
└───────────────────────────────────────────────────────────┘
```

### Komponen
| Komponen | Tipe | Catatan |
|---|---|---|
| Heading "Pembayaran Honor" | Text | |
| List item penerima | Row × N | Nama + tombol Bayar |
| Tombol "Bayar" | Button | Menuju halaman Detail Penerima |

### Behavior
- Klik **Bayar** → navigasi ke halaman **Detail Penerima Honor** (sesuai nama)

---

## 3. Detail Penerima Honor

**Tujuan:** Lihat & atur rincian gaji penerima, lalu bayar.

```
┌──────────────┬────────────────────────────────────────────┐
│ NAVBAR                                                    │
├──────────────┼────────────────────────────────────────────┤
│ SIDEBAR      │                                            │
│              │  Guru B                                    │
│ • Dashboard  │  ─────────────────────                     │
│ • Honor      │  Total Hasil    : Rp xxx                   │
│ • Laporan    │  Total Terbayar : Rp xxx                   │
│ • ...        │  Total SPP/Honor: Rp xxx                   │
│              │                                            │
│              │  Detail Gaji                               │
│              │  ─────────────────                         │
│              │  Gaji Pokok  : Rp 100.000  [isi gaji]      │
│              │  Tunjangan   : Rp  40.000  [isi gaji]      │
│              │  Potongan    : Rp  20.000  [isi gaji]      │
│              │  ─────────────────                         │
│              │  TOTAL       : Rp 120.000                  │
│              │                                            │
│              │              [ Bayar Sekarang ]            │
│              │                                            │
└──────────────┴────────────────────────────────────────────┘
```

### Komponen
| Komponen | Tipe | Catatan |
|---|---|---|
| Sidebar navigasi | Side menu | |
| Header nama penerima | Text besar | "Guru B" / "Kepala Sekolah" |
| Ringkasan total | Info block | Total hasil, terbayar, SPP/honor |
| Detail Gaji | Table/List | 3 komponen + total |
| Field gaji | Input + tombol "isi gaji" | Klik buka Pop-up Isi Gaji |
| Tombol "Bayar Sekarang" | Button (primary) | Konfirmasi & potong saldo |

### Behavior
- Jika field gaji kosong → tampilkan tombol **[isi gaji]** untuk membuka pop-up
- Jika sudah terisi → tampilkan nominal + bisa diklik untuk edit
- Total dihitung otomatis: `Pokok + Tunjangan − Potongan`
- Tombol **Bayar Sekarang** disable jika ada komponen yang masih kosong

---

## 4. Pop-up Input Dana

**Tujuan:** Mencatat dana masuk dari sumber tertentu (SPP/BOP/BOSDA/HONDA).

```
        ┌───────────────────────────────┐
        │  Input Dana                 × │
        │  ─────────────────────────    │
        │                               │
        │  Nominal                      │
        │  ┌─────────────────────────┐  │
        │  │ Rp                      │  │
        │  └─────────────────────────┘  │
        │                               │
        │  Upload File                  │
        │  ┌─────────────────────────┐  │
        │  │  📎 Pilih file...       │  │
        │  └─────────────────────────┘  │
        │                               │
        │          [ SUBMIT ]           │
        │                               │
        └───────────────────────────────┘
```

### Komponen
| Field | Tipe | Required | Catatan |
|---|---|---|---|
| Nominal | Number / Currency | ✅ | Format Rupiah |
| Upload File | File input | ✅ | Bukti transfer/kuitansi |
| Tombol Submit | Button | — | Simpan & tutup pop-up |

### Behavior
- Validasi: nominal > 0 & file ter-upload
- Setelah submit → pop-up tertutup, dashboard refresh

> **TODO:** Tambahkan field "Sumber Dana" (dropdown: SPP/BOP/BOSDA/HONDA) — di sketsa belum ada, tapi diperlukan untuk routing logic bisnis.

---

## 5. Pop-up Isi Gaji

**Tujuan:** Mengisi nominal salah satu komponen gaji (Pokok / Tunjangan / Potongan).

```
        ┌───────────────────────────────┐
        │  Isi Gaji                   × │
        │  ─────────────────────────    │
        │                               │
        │  Nominal                      │
        │  ┌─────────────────────────┐  │
        │  │ Rp                      │  │
        │  └─────────────────────────┘  │
        │                               │
        │          [ SUBMIT ]           │
        │                               │
        └───────────────────────────────┘
```

### Komponen
| Field | Tipe | Required |
|---|---|---|
| Nominal | Number / Currency | ✅ |
| Tombol Submit | Button | — |

### Behavior
- Setelah submit → pop-up tertutup, nominal muncul di baris gaji yang sesuai
- Total otomatis ter-update

---

## Konvensi UI

| Elemen | Style |
|---|---|
| Navbar | Selalu di atas, fixed |
| Sidebar | Hanya muncul di halaman detail (opsional di mobile) |
| Primary button | "Bayar Sekarang", "SUBMIT" — warna kontras |
| Format mata uang | `Rp 100.000` (titik sebagai pemisah ribuan) |
| Pop-up | Center, overlay gelap di belakang |

---

## Open Questions / TODO Desain

- [ ] Mobile layout: sidebar jadi bottom nav atau hamburger?
- [ ] Empty state: bagaimana tampilan jika belum ada riwayat?
- [ ] Loading state untuk submit
- [ ] Konfirmasi sebelum "Bayar Sekarang" (dialog "Yakin?")
- [ ] Pesan sukses setelah pembayaran
- [ ] Warna & branding (belum ditentukan)
