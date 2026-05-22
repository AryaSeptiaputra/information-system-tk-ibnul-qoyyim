# Wireframe — Halaman Headmaster (Role: headmaster)

> Draft wireframe untuk **4 halaman role headmaster** di TK Ibnul Qoyyim.
> Mengikuti design system di `wireframe-bendahara-pages.md`.

---

## Decisions Locked

- ✅ **Pendaftaran approval**: tidak di halaman headmaster (final approve = bendahara, sesuai Task #34)
- ✅ **Pengumuman**: tidak diimplementasi (defer)
- ✅ **Export PDF**: defer — pakai CSV existing untuk semua laporan

## Sidebar Headmaster (4 Item)

```
┌──────────────────────────┐
│ 📊 Dashboard             │  ← /admin/headmaster
│ 📋 Laporan Bulanan       │  ← /admin/headmaster/reports
│ 👨‍🏫 Daftar Guru          │  ← /admin/teachers (existing, view-only)
│ 🧒 Daftar Murid          │  ← /admin/students (existing, view-only)
└──────────────────────────┘
```

---

## 1. Dashboard Headmaster

**URL:** `/admin/headmaster`
**Tujuan:** Snapshot KPI sekolah — visual chart + summary cards.

```
┌─ Dashboard Headmaster ──────────────────────────────────┐
│                                                         │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐    │
│  │ TOTAL    │ │ TOTAL    │ │ KELAS    │ │ TAGIHAN  │    │
│  │ MURID    │ │ GURU     │ │ AKTIF    │ │ BELUM    │    │
│  │ 25       │ │ 8        │ │ 3        │ │ LUNAS    │    │
│  │          │ │          │ │          │ │ 12       │    │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘    │
│                                                         │
│  ┌─ Kehadiran Bulan Ini ──────────────────────────────┐ │
│  │  Donut: %hadir / izin / sakit / alpa per kelas     │ │
│  └────────────────────────────────────────────────────┘ │
│                                                         │
│  ┌─ Trend Honor 6 Bulan ──────────────────────────────┐ │
│  │  Line chart: total honor per bulan                 │ │
│  └────────────────────────────────────────────────────┘ │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Komponen
- 4 StatCard KPI (Murid / Guru / Kelas Aktif / Tagihan Belum Lunas)
- Donut chart kehadiran murid bulan ini (per status)
- Line chart trend total honor 6 bulan

---

## 2. Laporan Bulanan

**URL:** `/admin/headmaster/reports`
**Tujuan:** Ringkasan honor + kehadiran + keuangan untuk 1 bulan terpilih.

```
┌─ Laporan Bulanan ────────────────── [ Export CSV ]    ┐
│                                                       │
│  [ Bulan: Mei 2026 ▾ ]                                │
│                                                       │
│  ┌─ Ringkasan Keuangan ─────────────────────────────┐ │
│  │ Dana masuk       Rp 12.000.000                   │ │
│  │ Dana keluar      Rp  9.000.000                   │ │
│  │ Net              Rp  3.000.000                   │ │
│  │ Saldo akhir      Rp 25.000.000                   │ │
│  └──────────────────────────────────────────────────┘ │
│                                                       │
│  ┌─ Ringkasan Honor Guru ───────────────────────────┐ │
│  │ Total honor periode    Rp 8.500.000              │ │
│  │ Sudah dibayar          Rp 5.000.000  (5 guru)    │ │
│  │ Belum dibayar          Rp 3.500.000  (3 guru)    │ │
│  └──────────────────────────────────────────────────┘ │
│                                                       │
│  ┌─ Ringkasan Kehadiran Guru ───────────────────────┐ │
│  │  Guru     Hadir  Izin  Sakit  Alpa  Telat        │ │
│  │  Guru A   18     1     0      0     1            │ │
│  │  Guru B   20     0     0      0     0            │ │
│  │  ...                                             │ │
│  └──────────────────────────────────────────────────┘ │
│                                                       │
└───────────────────────────────────────────────────────┘
```

### Komponen
- Filter bulan (auto-submit)
- 3 section: Keuangan / Honor Guru / Kehadiran Guru
- Tabel kehadiran (5-6 kolom)
- Export CSV (gabungan 3 section atau per section)

---

## 3. Daftar Guru (read-only)

**URL:** `/admin/teachers` (route existing, render khusus untuk headmaster)
**Tujuan:** Lihat profil guru + masa kerja.

```
┌─ Daftar Guru ─────────────────────── [ Export CSV ]  ┐
│                                                      │
│  [ cari guru... ]              [ Status: Aktif ▾ ]   │
│                                                      │
│  Nama         Jabatan      NIP        Masa Kerja     │
│  ────────────────────────────────────────────────    │
│  Bunda Suci   Wali A       12345...   3 tahun        │
│  ...                                                 │
│                                                      │
└──────────────────────────────────────────────────────┘
```

### Catatan
- Route `admin.teachers.index` middleware sudah include `headmaster` (view-only)
- View existing pakai pattern lama (admin-section-header + management-table) — perlu polish ke UI baru
- Tombol CRUD (Edit/Hapus) **tidak muncul** untuk headmaster (controller-level role check)

---

## 4. Daftar Murid (read-only)

**URL:** `/admin/students` (route existing, render khusus untuk headmaster)
**Tujuan:** Lihat status & ringkasan murid.

```
┌─ Daftar Murid ─────────────────────── [ Export CSV ] ┐
│                                                      │
│  [ cari murid... ]              [ Grup: Semua ▾ ]    │
│                                                      │
│  Nama          Gender  Grup   Orangtua    Status     │
│  ────────────────────────────────────────────────    │
│  Ahmad         L       A      Bpk Yusuf   Aktif      │
│  ...                                                 │
│                                                      │
└──────────────────────────────────────────────────────┘
```

### Catatan
- Sama seperti Daftar Guru: read-only untuk headmaster
- Polish view ke pattern UI baru

---

## Halaman yang Reuse Backend Existing

| Halaman | Controller | Status |
|---|---|---|
| Dashboard Headmaster | (BARU) HeadmasterDashboardController | Aggregate dari semua model |
| Laporan Bulanan | (BARU) HeadmasterReportController | Aggregate per bulan + CSV export |
| Daftar Guru | TeacherManagementController (view only) | Polish view + role-aware UI |
| Daftar Murid | StudentManagementController (view only) | Polish view + role-aware UI |

## Task Plan

1. Sidebar headmaster restructure (4 menu)
2. Halaman 1: Dashboard Headmaster (controller baru + view chart)
3. Halaman 2: Laporan Bulanan (controller baru + view + CSV export)
4. Halaman 3: Daftar Guru polish (view-only friendly)
5. Halaman 4: Daftar Murid polish (view-only friendly)
