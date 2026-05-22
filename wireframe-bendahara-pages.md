# Wireframe — Halaman Bendahara (Role: administration)

> Draft wireframe untuk **9 halaman role administration (Bendahara)** di TK Ibnul Qoyyim.
> Dokumen ini adalah blueprint sebelum implementasi. Setelah disetujui, dijadikan acuan kerja.

---

## 0. Design System (Berlaku Semua Role)

### Layout Shell

```
┌──────────────────────────────────────────────────────────────┐
│  [Logo] TK Ibnul Qoyyim          [🔔]  [Avatar] Nama User ▾  │  ← Top navbar (fixed)
├──────────┬───────────────────────────────────────────────────┤
│          │                                                   │
│ Sidebar  │  Content area                                     │
│          │                                                   │
│ • Item 1 │  ┌────────────────────────────────────────────┐   │
│ • Item 2 │  │ Judul Halaman             [Primary Action] │   │  ← Page header
│ • Item 3 │  └────────────────────────────────────────────┘   │
│   ...    │                                                   │
│          │  [Tab1] [Tab2] [Tab3]   (jika ada)                │
│          │                                                   │
│          │  [Search] [Filter]      (toolbar jika ada)        │
│          │                                                   │
│          │  ┌────────────────────────────────────────────┐   │
│          │  │ Body (tabel / form / cards)                │   │
│          │  └────────────────────────────────────────────┘   │
│ [Logout] │                                                   │
└──────────┴───────────────────────────────────────────────────┘
```

### Warna

| Token | Hex | Penggunaan |
|---|---|---|
| Primary | `#2E7D32` (hijau) | Tombol utama, sidebar active, link |
| Primary-soft | `#E8F5E9` | Background card aktif, badge sukses |
| Danger | `#C62828` | Tombol hapus, badge bahaya |
| Warning | `#F57C00` | Badge perhatian |
| Neutral-50 | `#FAFAFA` | Background page |
| Neutral-100 | `#F5F5F5` | Background sidebar |
| Neutral-700 | `#424242` | Teks utama |
| Neutral-400 | `#9E9E9E` | Teks sekunder, hint |

### Typography

| Use | Font | Size | Weight |
|---|---|---|---|
| Page title | Inter | 24px | 700 |
| Section heading | Inter | 16px | 600 |
| Body | Inter | 14px | 400 |
| Caption / hint | Inter | 12px | 400 |

### Komponen Reusable

- **StatCard**: card kecil dengan icon + label + nominal besar
- **DataTable**: header + body + pagination, max 6 kolom essential
- **Modal**: overlay gelap + dialog center, max 480px lebar
- **Tab**: horizontal, underline + bold aktif
- **Badge**: pill kecil, warna sesuai status
- **EmptyState**: icon abu-abu + 1 baris + 1 CTA
- **Toast**: top-right, auto-dismiss 3 detik

### Prinsip Clean Look

- Judul halaman tanpa subtitle
- Hint text hanya untuk constraint khusus (mis. "Max 2MB")
- Tabel max 6 kolom essential — sisanya di modal detail
- Tombol: ikon + 1-2 kata max
- Empty state 1 baris, bukan paragraf

---

## Sidebar Bendahara (9 Item Utama)

```
┌──────────────────────────┐
│ 🏦 Dashboard             │  ← /admin/bendahara
│ 📝 Pendaftaran           │  ← /admin/registrations
│ 💳 Tagihan Murid         │  ← /admin/student-payments
│ 💸 Honor Guru            │  ← /admin/bendahara/honors (tab Generate|Bayar)
│ 💼 Posisi & Tunjangan    │  ← /admin/positions (tab Posisi|Tunjangan|Penugasan)
│ 📌 Tarif Kehadiran       │  ← /admin/teacher-attendance-rates
│ 🏗️ Sarpras              │  ← /admin/facilities
│ 👨‍👩‍👧 Data Orangtua-Murid │  ← /admin/parents (tab Orangtua|Murid)
│ 📊 Riwayat Dana          │  ← /admin/bendahara/transactions
└──────────────────────────┘
```

---

## 1. Dashboard Bendahara

**URL:** `/admin/bendahara`
**Tujuan:** Snapshot saldo + outstanding honor + riwayat singkat.

```
┌─ Dashboard Bendahara ───────────────────────────────────────┐
│                                                             │
│  Total Dana: Rp 25.000.000                                  │
│                                                             │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐      │
│  │ SPP  │ │ BOP  │ │BOSDA │ │HONDA │ │Gedung│ │ATK   │      │
│  │5.0jt │ │3.0jt │ │8.0jt │ │2.0jt │ │4.0jt │ │1.0jt │      │
│  │ + In │ │ + In │ │ + In │ │ + In │ │ + In │ │ + In │      │
│  └──────┘ └──────┘ └──────┘ └──────┘ └──────┘ └──────┘      │
│  ┌──────┐ ┌──────┐ ┌──────┐                                 │
│  │Serag │ │Sarana│ │Lain  │                                 │
│  │ 1.0jt│ │ 1.0jt│ │ 0    │                                 │
│  │ + In │ │ + In │ │ + In │                                 │
│  └──────┘ └──────┘ └──────┘                                 │
│                                                             │
│  ┌─ Honor Outstanding (4) ──────────────────────────────┐   │
│  │ Guru A    Rp   900.000     [Bayar]                   │   │
│  │ Guru B    Rp 1.000.000     [Bayar]                   │   │
│  │ Guru C    Rp   850.000     [Bayar]                   │   │
│  │                                  [Lihat semua →]     │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─ 5 Transaksi Terakhir ──────────────────────────────┐    │
│  │ +Rp 5jt   SPP        Pembayaran Guru A   22 Mei     │    │
│  │ -Rp 900k  BOP        Honor Guru A        22 Mei     │    │
│  │ ...                                                 │    │
│  │                                  [Riwayat lengkap →]│    │
│  └─────────────────────────────────────────────────────┘    │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Komponen
- **Total Dana** (text 32px bold)
- **9 StatCard** sumber dana (grid responsif, click "+ In" → modal Input Dana)
- **Honor Outstanding** (compact list, max 5 + link "Lihat semua")
- **Riwayat singkat** (5 terakhir + link)

### Modal Input Dana (re-used semua card)
```
┌─ Input Dana — BOSDA ──────────────┐
│ Nominal      [ Rp _________ ]     │
│ Tanggal      [ 2026-05-22 ▾ ]     │
│ Keterangan   [ _____________ ]    │
│ Bukti        [ 📎 Pilih file ]    │
│                                   │
│              [Batal]  [Simpan]    │
└───────────────────────────────────┘
```

---

## 2. Pendaftaran

**URL:** `/admin/registrations`
**Tujuan:** Proses calon murid (lihat, approve, reject).

```
┌─ Pendaftaran ──────────────────────────────────────────────┐
│                                                            │
│  [ Pending (8) ]  [ Approved (45) ]  [ Rejected (3) ]      │
│  ─────────────                                             │
│                                                            │
│  [ cari nama... ]                       [ Per: 25 ▾ ]      │
│                                                            │
│  Nama Calon       Grup   Pendaftar       Tgl       Aksi    │
│  ─────────────────────────────────────────────────────     │
│  Ahmad Fauzi      A      Bapak Yusuf     20 Mei    Lihat   │
│  Siti Aminah      B      Ibu Sarah       21 Mei    Lihat   │
│                                                            │
└────────────────────────────────────────────────────────────┘
```

### Tab
- **Pending** (default) — perlu action
- **Approved**, **Rejected** — riwayat

### Modal Detail Calon
- Data lengkap calon + orangtua
- Status pembayaran pendaftaran
- Tombol [Approve] [Reject]

---

## 3. Tagihan Murid

**URL:** `/admin/student-payments`
**Tujuan:** Input tagihan, approve bukti pembayaran, lihat status.

```
┌─ Tagihan Murid ─────────────── [ + Buat Tagihan ]          ┐
│                                                            │
│  [ Belum Lunas (12) ] [ Lunas (50) ] [ Menunggu Approve (3)]│
│  ─────────────────                                         │
│                                                            │
│  [ cari murid... ]   [ Jenis: Semua ▾ ]  [ Per: 25 ▾ ]     │
│                                                            │
│  Murid         Jenis        Nominal      Status     Aksi   │
│  ──────────────────────────────────────────────────────    │
│  Ahmad         SPP Mei      Rp 150k      Pending    Bayar  │
│  Siti          Iuran Masuk  Rp 1jt       Approve?   Cek    │
│                                                            │
└────────────────────────────────────────────────────────────┘
```

### Tab
- **Belum Lunas** (yang masih unpaid)
- **Lunas** (riwayat sukses)
- **Menunggu Approve** (ada bukti dari ortu, perlu di-approve bendahara)

### Modal Detail Tagihan
- Detail tagihan + komponen breakdown
- Riwayat cicilan (jika ada)
- Tombol [Approve Bukti] [Reject Bukti]

---

## 4. Honor Guru (Gabungan: Generate + Bayar)

**URL:** `/admin/bendahara/honors`
**Tujuan:** Hitung honor periode + bayar dengan split sumber dana.

```
┌─ Honor Guru ────────────────────── [ + Generate Honor ]    ┐
│                                                            │
│  [ Daftar Honor ]  [ Pembayaran ]                          │
│  ───────────────                                           │
│                                                            │
│  [ cari guru... ]   [ Bulan: Mei 2026 ▾ ]                  │
│                                                            │
│  Guru          Periode    Hadir   Total          Status    │
│  ─────────────────────────────────────────────────────     │
│  Guru A        05/2026    18      Rp 900.000     Unpaid    │
│  Guru B        05/2026    20      Rp 1.000.000   Paid      │
│                                                            │
└────────────────────────────────────────────────────────────┘
```

### Tab "Daftar Honor"
- List semua honor row periode terpilih
- Tombol Generate untuk buat honor row baru per guru

### Tab "Pembayaran"
- Hanya unpaid
- Tombol [Bayar] per row → halaman Detail Pembayaran

### Halaman Detail Bayar Honor

```
┌─ Bayar Honor: Guru A · 05/2026 ──────────────[ ← Kembali ] ┐
│                                                            │
│  ┌─ Statistik Absensi ──────────────────────────────────┐  │
│  │ Hadir 18  Izin 1  Sakit 0  Alpa 0  Telat 1           │  │
│  │ Kredit Libur 2  •  Hadir Efektif 20                  │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                            │
│  ┌─ Detail Gaji ────────────────────────────────────────┐  │
│  │ Gaji Pokok       Rp 1.000.000        [Edit]          │  │
│  │ Tunjangan        Rp   200.000        [Edit]          │  │
│  │ Potongan       − Rp    10.000        [Edit]          │  │
│  │ ─────────────────────────────────────                │  │
│  │ TOTAL            Rp 1.190.000                        │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                            │
│  ┌─ Alokasi Sumber Dana (total harus Rp 1.190.000) ────┐   │
│  │ SPP    [ 500.000 ]  (saldo 5.0jt) [Hapus]           │   │
│  │ BOP    [ 690.000 ]  (saldo 3.0jt) [Hapus]           │   │
│  │ [+ Tambah Alokasi]                                  │   │
│  │ Sisa: Rp 0  ✓                                       │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                            │
│  Tanggal Bayar: [ 2026-05-22 ▾ ]                           │
│                                                            │
│                                          [ Bayar Sekarang ]│
└────────────────────────────────────────────────────────────┘
```

### Komponen
- Statistik Absensi (compact, 1 baris)
- Detail Gaji (3 baris + total, klik Edit → modal Isi Gaji)
- Alokasi Sumber Dana (split, dengan live sisa)
- Bayar Sekarang (disabled jika sisa ≠ 0)

---

## 5. Posisi & Tunjangan (Gabungan: Posisi + Jenis Tunjangan + Penugasan)

**URL:** `/admin/positions`
**Tujuan:** Master posisi, master jenis tunjangan, nominal per posisi, assign guru.

```
┌─ Posisi & Tunjangan ─────────────────── [ + Tambah ]      ┐
│                                                           │
│  [ Posisi ]  [ Jenis Tunjangan ]  [ Nominal & Penugasan ] │
│  ────────                                                 │
│                                                           │
│  [ cari... ]                                              │
│                                                           │
│  Nama Posisi          Status      Jumlah Guru   Aksi      │
│  ───────────────────────────────────────────────────      │
│  Bendahara            Aktif       1             Edit Hapus│
│  Kepala Sekolah       Aktif       1             Edit Hapus│
│  Wali Kelas           Aktif       3             Edit Hapus│
│                                                           │
└───────────────────────────────────────────────────────────┘
```

### Tab "Posisi"
- List master posisi (CRUD)

### Tab "Jenis Tunjangan"
- List master jenis tunjangan (CRUD)

### Tab "Nominal & Penugasan"
- Cross-tab: posisi × jenis tunjangan = nominal
- Per posisi, tampil daftar guru yang ditugaskan + tombol [+ Assign Guru]

---

## 6. Tarif Kehadiran

**URL:** `/admin/teacher-attendance-rates`
**Tujuan:** Set rate/hari kehadiran per guru (effective period).

```
┌─ Tarif Kehadiran ──────────────────── [ + Set Tarif ]     ┐
│                                                           │
│  [ cari guru... ]                                         │
│                                                           │
│  Guru          Rate/Hari       Berlaku Dari   Sampai      │
│  ──────────────────────────────────────────────────       │
│  Guru A        Rp 50.000       2026-01-01     —    Edit   │
│  Guru B        Rp 50.000       2026-01-01     —    Edit   │
│  Guru C        Rp 60.000       2026-03-01     —    Edit   │
│                                                           │
└───────────────────────────────────────────────────────────┘
```

### Modal Set Tarif
- Pilih guru (dropdown searchable)
- Nominal per hari
- Berlaku dari, sampai (opsional)

---

## 7. Sarpras

**URL:** `/admin/facilities`
**Tujuan:** Catat sarana prasarana sekolah.

```
┌─ Sarpras ──────────────────────────── [ + Tambah ]        ┐
│                                                           │
│  [ cari... ]  [ Kondisi: Semua ▾ ]  [ Status: Aktif ▾ ]   │
│                                                           │
│  Barang          Jumlah   Kondisi      Tahun   Aksi       │
│  ──────────────────────────────────────────────────       │
│  Meja Belajar    25       Baik         2024    Edit Hapus │
│  Kursi           50       Rusak Ringan 2022    Edit Hapus │
│                                                           │
└───────────────────────────────────────────────────────────┘
```

---

## 8. Data Orangtua-Murid (Gabungan)

**URL:** `/admin/parents`
**Tujuan:** Data orangtua + relasi ke murid.

```
┌─ Data Orangtua-Murid ──────────────── [ + Tambah ]        ┐
│                                                           │
│  [ Orangtua ]  [ Murid ]                                  │
│  ──────────                                               │
│                                                           │
│  [ cari... ]                                              │
│                                                           │
│  Nama Ayah        Nama Ibu      Anak             Aksi     │
│  ──────────────────────────────────────────────────       │
│  Bapak Yusuf      Ibu Sarah     Ahmad, Siti      Lihat    │
│  Bapak Anwar      Ibu Diah      Fauzi            Lihat    │
│                                                           │
└───────────────────────────────────────────────────────────┘
```

### Tab "Orangtua"
- List orangtua + anak terkait

### Tab "Murid"
- List murid + parent terkait

---

## 9. Riwayat Dana

**URL:** `/admin/bendahara/transactions`
**Tujuan:** Laporan transaksi dana keluar/masuk (audit + reconciliation).

```
┌─ Riwayat Dana ───────────────────── [ Export CSV ]        ┐
│                                                           │
│  [ Sumber: Semua ▾ ]  [ Arah: Semua ▾ ]  [ Tgl: Mei ▾ ]   │
│                                                           │
│  Tgl       Sumber   Arah    Nominal      Keterangan       │
│  ──────────────────────────────────────────────────       │
│  22 Mei    SPP      Masuk   +Rp 150k     SPP Ahmad Mei    │
│  22 Mei    BOP      Keluar  −Rp 900k     Honor Guru A     │
│  21 Mei    BOSDA    Masuk   +Rp 5jt      Transfer dinas   │
│                                                           │
│  Total Masuk: Rp X.XXX.XXX                                │
│  Total Keluar: Rp Y.YYY.YYY                               │
│  Net: Rp Z.ZZZ.ZZZ                                        │
│                                                           │
└───────────────────────────────────────────────────────────┘
```

---

## Konvensi yang Tetap Sama di Semua Halaman

| Aspek | Aturan |
|---|---|
| Page header | Judul kiri + 1 tombol primary kanan (jika ada) |
| Tab | Horizontal, max 3 tab |
| Toolbar | Search kiri, filter kanan |
| Tabel | Max 6 kolom, action di kolom terakhir |
| Empty state | Icon + 1 baris + 1 CTA |
| Modal | Center, max 480px, overlay gelap |
| Success feedback | Toast hijau top-right 3 detik |
| Konfirmasi destruktif | Modal "Yakin?" sebelum hapus / bayar |
| Format Rupiah | `Rp 1.500.000` (titik separator) |

---

## Halaman yang Reuse Backend Existing

| Halaman | Controller Existing | Catatan |
|---|---|---|
| Dashboard Bendahara | BendaharaDashboardController | Polish UI |
| Honor Guru (tab Generate) | TeacherHonorManagementController | Bungkus dalam halaman gabungan |
| Honor Guru (tab Pembayaran) | BendaharaHonorController | Bungkus dalam halaman gabungan |
| Posisi & Tunjangan | PositionManagementController + AllowanceTypeManagementController + PositionAllowanceManagementController + TeacherPositionManagementController | Bungkus dalam 1 halaman 3-tab |
| Tagihan Murid | StudentPaymentManagementController | Polish UI + tambah tab |
| Tarif Kehadiran | TeacherAttendanceRateManagementController | Polish UI |
| Sarpras | FacilityManagementController | Polish UI |
| Pendaftaran | RegistrationManagementController | Polish UI + tambah tab |
| Data Orangtua-Murid | ParentManagementController + StudentManagementController | Bungkus dalam halaman 2-tab |
| Riwayat Dana | (perlu controller baru) BendaharaTransactionController | Sebagian besar view-only |

---

## Decisions (Locked)

- ✅ **Riwayat Dana**: pakai **chart saja** (donut per sumber + line trend bulanan). Tabel dihilangkan dari halaman ini — kalau perlu detail row, klik segment chart → drill-down modal.
- ✅ **Honor Detail**: `manual_adjustment` **diintegrasikan** ke dalam edit Pokok/Tunjangan/Potongan. Bendahara tidak melihat field "Penyesuaian Manual" terpisah — selisih masuk salah satu komponen secara transparan.
- ✅ **Pendaftaran**: **Bendahara yang final approve** (bukan headmaster). Headmaster hanya laporan/monitoring.
- ✅ **Modal**: pakai **Alpine.js** (sudah ada di `package.json` tapi belum aktif — perlu setup minimal). Pattern: `x-data="{ open: false }"` + `@click` + `x-show` + `@click.away`.

## Visualisasi Riwayat Dana

```
┌─ Riwayat Dana ───────────────────── [ Export CSV ]    ┐
│                                                       │
│  [ Periode: Mei 2026 ▾ ]                              │
│                                                       │
│  ┌─ Donut per Sumber ──────┐  ┌─ Trend 6 bulan ────┐  │
│  │                         │  │                    │  │
│  │      ╭─╮                │  │   ▁ ▃ ▆ █ ▇ ▅      │  │
│  │     │ X │  SPP 40%      │  │  Jan─────Jun       │  │
│  │      ╰─╯                │  │  Masuk vs Keluar   │  │
│  │  Click segment →        │  │                    │  │
│  │  modal detail           │  │                    │  │
│  └─────────────────────────┘  └────────────────────┘  │
│                                                       │
│  Total Masuk:  Rp X.XXX.XXX  ↑                        │
│  Total Keluar: Rp Y.YYY.YYY  ↓                        │
│  Net:          Rp Z.ZZZ.ZZZ                           │
│                                                       │
└───────────────────────────────────────────────────────┘
```

**Library chart**: Chart.js (lightweight, sudah common di Laravel project) atau ApexCharts (lebih interaktif). Default rekomendasi: **Chart.js** karena bundle lebih kecil.
