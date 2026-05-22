# Wireframe — Halaman Superadmin (Role: superadmin)

> Draft wireframe untuk **halaman role superadmin** di TK Ibnul Qoyyim.
> Mengikuti design system di `wireframe-bendahara-pages.md`.

## Decisions

- ✅ **Audit Log**: SKIP — di-defer (perlu package + observer + migration, scope besar)
- ✅ **Override Honor**: tidak buat halaman terpisah, sudah tersedia via tombol Edit di "Honor Guru" existing
- ✅ Banyak halaman reuse view yang sudah polished (Posisi, Tarif, Sarpras, Data Guru/Murid)

## Sidebar Superadmin (11 Item Final)

```
[Utama]
🏦 Dashboard

[Pengguna]
👥 Manajemen Pengguna

[Master Data]
🧒 Data Murid
👨‍🏫 Data Guru
🏗️ Sarpras
🗓️ Hari Libur          ← BARU

[Honor & Tunjangan]
💼 Posisi & Tunjangan
📌 Tarif Kehadiran
💰 Honor Guru

[Keuangan]
🧾 Master Payment
💳 Tagihan Murid

[Pengaturan]
⚙️ Info Pembayaran
```

## Halaman BARU yang Perlu Dibuat

### Hari Libur (CRUD)

**URL:** `/admin/holidays`
**Backend:** ada (model `Holiday` + table + seeder dari Task #14).
**Yang perlu:** controller + view + route + sidebar entry.

```
┌─ Hari Libur ──────────────────────── [ + Tambah ]      ┐
│                                                        │
│  [ cari... ]                          [ Tahun: 2026 ▾ ]│
│                                                        │
│  Tanggal       Nama Libur                 Status       │
│  ─────────────────────────────────────────             │
│  2026-01-01    Tahun Baru Masehi          Aktif        │
│  2026-05-01    Hari Buruh Internasional   Aktif        │
│  2026-06-01    Hari Lahir Pancasila       Aktif        │
│  2026-08-17    Hari Kemerdekaan RI        Aktif        │
│  2026-12-25    Hari Raya Natal            Aktif        │
│                                                        │
└────────────────────────────────────────────────────────┘
```

Modal CRUD: date + name + is_active toggle.

## Halaman yang Polish UI

| # | Halaman | View existing |
|---|---|---|
| Dashboard | dashboard/admin/admin-dashboard.blade.php atau index | Polish header + stat cards + recent activity |
| Manajemen Pengguna | dashboard/admin/users.blade.php | Polish pakai pattern UI baru |
| Master Payment | dashboard/admin/payments.blade.php | Polish |
| Info Pembayaran | dashboard/admin/payment-settings.blade.php | Polish |

## Task Plan

1. Sidebar superadmin restructure (11 menu final)
2. Hari Libur: controller + view + route (BARU)
3. Polish Dashboard superadmin
4. Polish Manajemen Pengguna
5. Polish Master Payment
6. Polish Info Pembayaran
