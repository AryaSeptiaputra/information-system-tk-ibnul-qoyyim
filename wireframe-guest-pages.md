# Wireframe — Halaman Guest / Orangtua (Role: guest)

> Draft wireframe untuk **4 halaman role guest** di TK Ibnul Qoyyim.
> Mengikuti design system di `wireframe-bendahara-pages.md`.

---

## Sidebar Guest (4 Item)

```
┌──────────────────────────┐
│ 🏠 Beranda               │  ← /dashboard
│ 💳 Tagihan & Bayar       │  ← /dashboard/bills
│ 📝 Absensi Anak          │  ← /dashboard/students
│ 👤 Profil & Info         │  ← /dashboard/info
└──────────────────────────┘
```

**Hapus** dari sidebar guest saat ini:
- Info Murid & Orang Tua (jadi gabung ke Profil & Info)
- Data Murid & Absensi (jadi "Absensi Anak")
- Tagihan (rename jadi "Tagihan & Bayar")

---

## 1. Beranda (Dashboard)

**URL:** `/dashboard`
**Tujuan:** Welcome + ringkasan tagihan + ringkasan anak.

```
┌─ Selamat datang, [Nama Ortu] ─────────────────────────┐
│                                                       │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐               │
│  │ TAGIHAN  │ │ ANAK     │ │ ABSENSI  │               │
│  │ BELUM    │ │ TERDAFTAR│ │ BULAN INI│               │
│  │ LUNAS    │ │ 2 anak   │ │ ...      │               │
│  │ 3 tagihan│ │          │ │          │               │
│  └──────────┘ └──────────┘ └──────────┘               │
│                                                       │
│  ┌─ Tagihan yang Perlu Dibayar ─────────────────┐     │
│  │ Anak 1 · SPP Mei  Rp 150k       [ Bayar ]    │     │
│  │ Anak 2 · Iuran    Rp 1jt        [ Bayar ]    │     │
│  └──────────────────────────────────────────────┘     │
│                                                       │
└───────────────────────────────────────────────────────┘
```

### Komponen
- 3 StatCard ringkas
- Section "Tagihan Perlu Dibayar" — max 5 + link lihat semua
- (Kalau pendaftaran masih proses) → tampilkan status registrasi anak

---

## 2. Tagihan & Bayar

**URL:** `/dashboard/bills`
**Tujuan:** List tagihan + bayar dengan upload bukti.

```
┌─ Tagihan & Bayar ───────────────────────────────────  ┐
│                                                       │
│  [ Belum Lunas (3) ] [ Menunggu Approve ] [ Lunas ]   │
│  ────────────────                                     │
│                                                       │
│  ┌─ Anak 1 ─ Ahmad Fauzi ──────────────────────┐      │
│  │ SPP Mei 2026         Rp 150.000   [Bayar]   │      │
│  │ Iuran Masuk          Rp 1.000.000 [Bayar]   │      │
│  └─────────────────────────────────────────────┘      │
│                                                       │
│  ┌─ Anak 2 ─ Siti Aminah ──────────────────────┐      │
│  │ SPP Mei 2026         Rp 150.000   [Bayar]   │      │
│  └─────────────────────────────────────────────┘      │
│                                                       │
└───────────────────────────────────────────────────────┘
```

### Komponen
- Tab status (Belum Lunas / Menunggu Approve / Lunas)
- Grouped per anak
- Tombol Bayar → modal upload bukti (existing form bisa reuse)

---

## 3. Absensi Anak

**URL:** `/dashboard/students`
**Tujuan:** Lihat profil + riwayat absensi anak.

```
┌─ Absensi Anak ──────────────────────────────────────  ┐
│                                                       │
│  [ Anak 1 ] [ Anak 2 ]                                │
│  ──────────                                           │
│                                                       │
│  Ahmad Fauzi · L · Grup A · Aktif                     │
│                                                       │
│  Ringkasan 60 hari terakhir:                          │
│  18 Hadir · 1 Izin · 2 Sakit · 0 Alpa                 │
│                                                       │
│  Riwayat Absensi                                      │
│  Tgl       Status      Keterangan                     │
│  ──────────────────────────────                       │
│  22 Mei    Hadir       -                              │
│  21 Mei    Izin        Sakit demam                    │
└───────────────────────────────────────────────────────┘
```

### Komponen
- Tab per anak (jika > 1)
- Card profil singkat
- Stat absensi ringkas
- Tabel riwayat

---

## 4. Profil & Info

**URL:** `/dashboard/info`
**Tujuan:** Profil orangtua + info sekolah.

```
┌─ Profil & Info ─────────────────────────────────────  ┐
│                                                       │
│  ┌─ Data Akun ────────────────────────────────────┐   │
│  │ Email      ortu@email.com                      │   │
│  │ Telepon    08xxx                               │   │
│  └────────────────────────────────────────────────┘   │
│                                                       │
│  ┌─ Data Orangtua / Wali ────────────────────────┐    │
│  │ Nama Ayah     Bpk Yusuf                       │    │
│  │ Pekerjaan     Wiraswasta                      │    │
│  │ Telepon       08xxx                           │    │
│  │ Nama Ibu      Ibu Sarah                       │    │
│  │ ...                                           │    │
│  │                                  [Edit Akun]  │    │
│  └───────────────────────────────────────────────┘    │
│                                                       │
│  ┌─ Anak yang Terdaftar ─────────────────────────┐    │
│  │ • Ahmad Fauzi · Grup A · Aktif                │    │
│  │ • Siti Aminah · Grup B · Aktif                │    │
│  └───────────────────────────────────────────────┘    │
│                                                       │
│  ┌─ Info Sekolah ────────────────────────────────┐    │
│  │ Nama: TK Ibnul Qoyyim Sulawesi                │    │
│  │ Alamat: ...                                   │    │
│  │ Kontak: ...                                   │    │
│  └───────────────────────────────────────────────┘    │
└───────────────────────────────────────────────────────┘
```

---

## Decisions

- ✅ Halaman registrasi calon murid (existing di `/dashboard`) tetap dipakai bagi guest yang belum punya anak terdaftar — auto-fallback di Beranda.
- ✅ Edit akun via tombol → link ke `/profile` existing (Laravel Breeze).
- ✅ Polish hanya wrapper UI (header, toast, section). Partial complex (registration form multi-step, upload bukti modal) reuse existing supaya tidak break.

## Task Plan

1. Sidebar guest restructure (4 menu)
2. Polish Beranda (dashboard.index untuk role guest)
3. Polish Tagihan & Bayar (wrapper)
4. Polish Absensi Anak (wrapper)
5. Polish Profil & Info (full rewrite — paling simple)
