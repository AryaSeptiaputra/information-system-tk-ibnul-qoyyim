# Wireframe — Halaman Teacher (Role: teacher)

> Draft wireframe untuk **5 halaman role teacher** di TK Ibnul Qoyyim.
> Mengikuti design system di `wireframe-bendahara-pages.md` (warna, typography, komponen UI reusable, Alpine.js, dst).

---

## Sidebar Teacher (5 Item)

```
┌──────────────────────────┐
│ 🏠 Dashboard Saya        │  ← /admin/teacher
│ 🕒 Absen Saya            │  ← /admin/my-attendance (existing)
│ 💰 Honor Saya            │  ← /admin/my-honor (existing)
│ 🧒 Murid Kelas Saya      │  ← /admin/teacher/students
│ 👤 Profil Saya           │  ← /admin/teacher/profile
└──────────────────────────┘
```

**Hapus** dari sidebar teacher saat ini:
- Data Siswa, Data Kelas (global → kewenangan admin)
- Absensi Murid (kalau dipakai untuk absen murid kelas sendiri, masuk ke "Murid Kelas Saya")
- Riwayat Absensi (Semua) (terlalu broad untuk teacher)

---

## 1. Dashboard Saya

**URL:** `/admin/teacher`
**Tujuan:** Snapshot personal — sapaan + 3 stat ringkas + 2 quick action.

```
┌─ Dashboard Saya ─────────────────────────────────────────┐
│                                                          │
│  Selamat datang, [Nama Guru]                             │
│                                                          │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐                  │
│  │ HONOR    │ │ ABSENSI  │ │ KELAS    │                  │
│  │ Bulan ini│ │ Bulan ini│ │ Saya     │                  │
│  │ Rp 900k  │ │ 18 hadir │ │ 2 kelas  │                  │
│  │ Unpaid   │ │ 1 izin   │ │ 25 murid │                  │
│  └──────────┘ └──────────┘ └──────────┘                  │
│                                                          │
│  ┌─ Aksi Cepat ──────────────────────────────────────┐   │
│  │ [ 🕒 Absen Sekarang ]   [ 📋 Lihat Honor Saya ]   │   │
│  └───────────────────────────────────────────────────┘   │
│                                                          │
│  ┌─ Absensi Saya 7 Hari Terakhir ────────────────────┐   │
│  │ Sen 19 Mei  Hadir 07:30                           │   │
│  │ Sel 20 Mei  Hadir 07:25                           │   │
│  │ Rab 21 Mei  Izin (lampiran)                       │   │
│  │ Kam 22 Mei  Hadir 08:05  ⚠️ Telat 5 mnt           │   │
│  │ ...                                               │   │
│  └───────────────────────────────────────────────────┘   │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

**Komponen:**
- Heading: "Selamat datang, [Nama]" (besar)
- 3 StatCard: Honor bulan ini, Absensi bulan ini (compact), Kelas Saya
- Aksi cepat: 2 tombol ke Absen Sekarang & Honor Saya
- Riwayat absensi 7 hari (mini table)

---

## 2. Absen Saya (existing — polish)

**URL:** `/admin/my-attendance`
**Tujuan:** Check-in mandiri, ajukan izin, lapor sakit.

**Polish:**
- Migrasi ke pattern `<x-ui.page-header>`, `<x-ui.section>`, `<x-ui.modal>` (Alpine)
- Hapus subtitle deskripsi
- Card status hari ini lebih prominent (badge besar Tepat Waktu / Telat / Belum Absen)
- Form Izin & Sakit → modal Alpine, bukan inline form 2-kolom
- Tabel riwayat: 5-6 kolom essential

```
┌─ Absen Saya ─────────────────────────────────────────────┐
│                                                          │
│  Senin, 22 Mei 2026 · 07:28 WITA                         │
│                                                          │
│  ┌─ Status Hari Ini ─────────────────────────────────┐   │
│  │ [ Hadir Tepat Waktu ✓ ] Check-in: 07:25 WITA      │   │
│  │   (atau)                                          │   │
│  │ [ Belum Absen ]                                   │   │
│  │   Jendela: 07:25 - 11:00 WITA                     │   │
│  │   [ Absen Sekarang ] [ Ajukan Izin ] [ Lapor Sakit] │  │
│  └───────────────────────────────────────────────────┘   │
│                                                          │
│  Riwayat Absensi                                         │
│  Tanggal    Status   Check-in  Telat   Bukti             │
│  ──────────────────────────────────────────              │
│  22 Mei     Hadir    07:25     -       -                 │
│  21 Mei     Izin     -         -       [Lihat]           │
└──────────────────────────────────────────────────────────┘
```

---

## 3. Honor Saya (existing — polish)

**URL:** `/admin/my-honor`
**Tujuan:** Lihat honor bulan berjalan + riwayat pencairan.

**Polish:**
- 1 StatCard besar: estimasi honor bulan ini + status
- Riwayat: tabel 5 kolom (Periode, Hadir, Nominal, Tanggal Bayar, Status)
- Hilangkan kolom internal (allowance breakdown detail, dll) — kalau teacher mau detail, klik row → modal detail

```
┌─ Honor Saya ─────────────────────────────────────────────┐
│                                                          │
│  ┌─ Bulan Berjalan (Mei 2026) ───────────────────────┐   │
│  │ Estimasi   Rp 900.000                             │   │
│  │ Status     Unpaid                                 │   │
│  │ Hadir      18 hari · Telat 1 hari                 │   │
│  └───────────────────────────────────────────────────┘   │
│                                                          │
│  Riwayat Pencairan                                       │
│  Periode    Hadir    Nominal       Tgl Bayar    Status   │
│  ──────────────────────────────────────────────────      │
│  04/2026    20       Rp 1.000.000  05 Mei       Paid     │
│  03/2026    19       Rp   950.000  05 Apr       Paid     │
└──────────────────────────────────────────────────────────┘
```

---

## 4. Murid Kelas Saya

**URL:** `/admin/teacher/students`
**Tujuan:** List murid di kelas yang guru ajar + akses cepat ke absensi murid.

```
┌─ Murid Kelas Saya ───────────────────────────────────────┐
│                                                          │
│  [ Kelas A (12) ] [ Kelas B (13) ]                       │
│  ──────────                                              │
│                                                          │
│  [ cari murid... ]                                       │
│                                                          │
│  Nama Murid     Gender  Orangtua          Status   Aksi  │
│  ────────────────────────────────────────────────────    │
│  Ahmad Fauzi    L       Bpk Yusuf/Ibu S   Aktif   Absen  │
│  Siti Aminah    P       Bpk Anwar/Ibu D   Aktif   Absen  │
│  ...                                                     │
└──────────────────────────────────────────────────────────┘
```

**Komponen:**
- Tab horizontal per kelas (kalau guru ajar > 1 kelas)
- Tabel 5 kolom: Nama / Gender / Orangtua / Status / Aksi
- Tombol "Absen" per murid → buka modal/halaman absensi cepat

---

## 5. Profil Saya

**URL:** `/admin/teacher/profile`
**Tujuan:** Lihat & edit data pribadi guru.

```
┌─ Profil Saya ──────────────────────────── [ Simpan ]    ┐
│                                                         │
│  ┌─ Data Akun ───────────────────────────────────────┐  │
│  │ Email      bunda@tk-iq.sch.id                     │  │
│  │ Role       Guru                                   │  │
│  │ Status     Aktif                                  │  │
│  └───────────────────────────────────────────────────┘  │
│                                                         │
│  ┌─ Data Pribadi (editable) ─────────────────────────┐  │
│  │ Nama Lengkap     [ Bunda Suci, S.Pd ]             │  │
│  │ Jabatan          [ Wali Kelas A ]                 │  │
│  │ NIP              [ ... ]                          │  │
│  │ NUPTK            [ ... ]                          │  │
│  │ Tempat Lahir     [ ... ]                          │  │
│  │ Tgl Lahir        [ ... ]                          │  │
│  │ Tgl Mulai Kerja  [ ... ]                          │  │
│  │ Masa Kerja       2 th 3 bln  (auto, readonly)     │  │
│  │ Pendidikan       [ S1 PGRA ]                      │  │
│  │ Telepon          [ ... ]                          │  │
│  └───────────────────────────────────────────────────┘  │
│                                                         │
│  ┌─ Ubah Password (opsional) ────────────────────────┐  │
│  │ Password Lama      [ ... ]                        │  │
│  │ Password Baru      [ ... ]                        │  │
│  │ Konfirmasi         [ ... ]                        │  │
│  └───────────────────────────────────────────────────┘  │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

## Halaman yang Reuse Backend Existing

| Halaman | Controller | Catatan |
|---|---|---|
| Dashboard Saya | (BARU) TeacherDashboardController | Aggregate dari TeacherHonor, TeacherAttendance, classes |
| Absen Saya | TeacherAttendanceSelfController | Polish view only |
| Honor Saya | TeacherHonorSelfController | Polish view only |
| Murid Kelas Saya | (BARU) TeacherStudentController | Filter students di kelas teacher |
| Profil Saya | (BARU) TeacherProfileController | Update TeacherDetail + User |

## Decisions (Locked)

- ✅ Polish 2 halaman existing (Absen Saya, Honor Saya) — pattern UI baru, hapus subtitle, Alpine modal
- ✅ Buat 3 controller baru (Dashboard, Students, Profile) khusus role teacher
- ✅ Tab per kelas di "Murid Kelas Saya" (jika guru ajar > 1 kelas)
- ✅ Profil: edit TeacherDetail + opsional ubah password
- ✅ Tidak ada akses ke modul global (Data Siswa, Data Kelas, dll)

## Decisions Locked (Final)

- ✅ **Tombol "Absen" di Murid Kelas Saya**: cukup **link ke halaman `admin.student-attendance.create`** existing dengan pre-filled student_id. Tidak ada workflow batch per kelas (defer kalau perlu).
- ✅ **Materi Ajar**: **drop** dari sidebar teacher (out of scope, route '#' placeholder tidak dipakai).
