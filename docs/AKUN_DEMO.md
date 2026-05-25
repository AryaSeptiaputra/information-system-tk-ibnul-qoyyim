# Daftar Akun Sistem

> Semua akun dummy menggunakan password: **`password`**
> Password superadmin dapat dikonfigurasi via `.env` (`SUPERADMIN_PASSWORD`).

---

## Akun Wajib (Core Required)

Dibuat oleh `CoreRequiredSeeder` → `SuperAdminUserSeeder`.
Selalu ada di setiap environment (production, staging, lokal).

| Nama | Email | Password | Role |
|---|---|---|---|
| Super Admin | `superadmin@gmail.com` | *(dari env `SUPERADMIN_PASSWORD`, default: `password`)* | `superadmin` |

> Email dan nama dapat diganti via `.env`:
> ```
> SUPERADMIN_EMAIL=superadmin@gmail.com
> SUPERADMIN_NAME=Super Admin
> SUPERADMIN_PASSWORD=ganti_ini
> ```

---

## Akun Demo / Testing

Dibuat oleh `DummyTestingSeeder` → `DemoUserSeeder`.
**Jangan dijalankan di production.**

### Staff & Guru

| Nama | Email | Password | Role | Keterangan |
|---|---|---|---|---|
| Admin Staff | `admin@example.com` | `password` | `administration` | Akses manajemen data sekolah |
| Kepala Sekolah | `headmaster@example.com` | `password` | `headmaster` | Akses dashboard & laporan |
| Bendahara Staff | `bendahara@example.com` | `password` | `bendahara` | Akses modul keuangan & honor |
| Ibu Samiyah, S.Pd | `teacher@example.com` | `password` | `teacher` | Wali Kelas A |
| Bapak Rahmat Hidayat | `teacher2@example.com` | `password` | `teacher` | Wali Kelas B / Bendahara |
| Ibu Yanti Rahayu | `teacher3@example.com` | `password` | `teacher` | Guru Pendamping |
| Guest User | `guest@example.com` | `password` | `guest` | Akses terbatas (orang tua) |

### Akun Pendaftar (RegistrationSeeder)

Akun guest yang memiliki data pendaftaran murid baru.

| Email | Password | Status Pendaftaran | Keterangan |
|---|---|---|---|
| `registrant1@example.com` | `password` | `pending` | Menunggu review admin |
| `registrant2@example.com` | `password` | `approved_awaiting_payment` | Disetujui, menunggu bayar |
| `registrant3@example.com` | `password` | `rejected` | Ditolak (dokumen tidak lengkap) |
| `registrant4@example.com` | `password` | `active` | Sudah aktif sebagai murid |

---

## Ringkasan Akses per Role

| Role | Modul yang Dapat Diakses |
|---|---|
| `superadmin` | Semua modul + manajemen user |
| `administration` | Manajemen guru, murid, pendaftaran, absensi, fasilitas |
| `headmaster` | Dashboard, laporan, absensi |
| `bendahara` | Keuangan, honor guru, dana masuk/keluar |
| `teacher` | Absensi diri sendiri (`/admin/my-attendance`) |
| `guest` | Pendaftaran murid, status tagihan |

---

## Cara Menjalankan Seeder

```bash
# Core saja (production-safe, idempotent)
php artisan db:seed --class=CoreRequiredSeeder

# Semua data dummy untuk development/testing
php artisan db:seed --class=DummyTestingSeeder

# Reset total + isi ulang
php artisan migrate:fresh --seed
```
