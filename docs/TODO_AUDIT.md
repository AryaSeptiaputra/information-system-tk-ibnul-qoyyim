# TODO & Audit Status

Dokumen ini berisi daftar tugas dari feedback user beserta status implementasinya.
Tanggal audit: 2026-05-25.

Legenda:
- ✅ **DONE** — sudah ada di kode saat ini
- ⚠️ **PARTIAL** — sebagian ada, sebagian belum
- ❌ **TODO** — belum diimplementasikan / masih bug

---

## [AKUN GUEST / ORANG TUA — Proses Registrasi]

### 📝 Form Registrasi Pendaftaran

| # | Item | Status | Catatan |
|---|------|--------|---------|
| 1 | Upload dokumen pendukung saat registrasi (KK + Pas Foto + Akta Kelahiran) | ✅ DONE | Implementasi lengkap di commit `[pending]`: <br>1. ✅ Dua migration: `2026_05_25_000001_add_kk_file_path_to_registrations_table.php` (kolom `kk_file_path`) dan `2026_05_25_000002_add_child_documents_to_registrations_table.php` (kolom `photo_file_path` + `birth_certificate_file_path`) <br>2. ✅ `Registration::$fillable` ditambahkan ketiga kolom <br>3. ✅ Tiga input file di **step 3 (Review)**, semua `required`: <br> &nbsp;&nbsp;• Kartu Keluarga — `accept="image/jpeg,png,application/pdf"`, maks 2MB <br> &nbsp;&nbsp;• Pas Foto Anak — `accept="image/jpeg,png"` (PDF tidak boleh), maks 2MB <br> &nbsp;&nbsp;• Akta Kelahiran — `accept="image/jpeg,png,application/pdf"`, maks 2MB <br>4. ✅ Validasi di `RegistrationController::validateStep(3)` untuk ketiga file dengan pesan error custom Indonesia <br>5. ✅ Form `enctype="multipart/form-data"`. File di-store ke disk public di folder masing-masing: `registrations-kk/`, `registrations-photos/`, `registrations-birth-cert/` <br>6. ✅ Modal detail admin (`modal-detail.blade.php`) menampilkan section **"Dokumen Pendukung"** dengan 3 baris (KK + Pas Foto + Akta) — preview gambar inline (180×140) atau link "📄 Lihat PDF", placeholder "Belum diunggah" jika kosong. |

---

## [AKUN BENDAHARA / ADMINISTRATOR]

### 🟢 Halaman Honor Guru

| # | Item | Status | Catatan |
|---|------|--------|---------|
| 1 | Potongan Rp 10.000 untuk setiap "telat absensi" guru | ✅ DONE | Sudah ada di `TeacherHonorManagementController::buildHonorSummary()`. Backend hitung `late_penalty = lateCount × 10.000`. Frontend recap card (commit `48d45b6`) sudah menampilkan baris **Jumlah Telat** dan **Potongan Telat (- Rp X)**. Penalti dikurangi dari `Preview Total Honor`. |
| 2 | Potongan Rp 10.000 untuk izin > 2 hari berturut-turut (mulai hari ke-3) | ✅ DONE | Backend hitung `permission_penalty` dengan grace period 2 hari (`nextWorkday()` untuk deteksi izin beruntun). Frontend baris **Potongan Izin Berlebih** sudah muncul di recap card. |

### 🏗️ Halaman Sarana Prasarana

| # | Item | Status | Catatan |
|---|------|--------|---------|
| 1 | Halaman ERROR setelah pull terbaru | ✅ DONE | Bug visual di-fix di `facilities.blade.php` — `<th>Foto</th>` ditambahkan sebagai kolom pertama (commit `[pending]`). Tabel sekarang aligned (7 sel data vs 7 header). |
| 2 | Gambar bisa ditampilkan di halaman daftar (thumbnail + buka detail) | ✅ DONE | Thumbnail 48×48 di baris tabel + klik untuk buka full-size. Modal detail preview 140×110. Header tabel sudah diperbaiki. |
| 3 | Kolom "Keterangan" — input manual catatan kondisi + dana perbaikan/penggantian | ⚠️ PARTIAL | **Catatan kondisi sudah selesai** (commit `[pending]`): <br>- Migration `2026_05_25_000003_add_condition_note_to_facilities_table.php` — kolom `condition_note` (text, nullable) <br>- `Facility::$fillable` ditambah `condition_note` <br>- Textarea input di modal form (placeholder contoh, hint penjelasan) <br>- Tampil di modal detail (`white-space:pre-wrap`) hanya jika ada isi <br>- Kolom "Catatan Kondisi" ditambahkan di CSV export <br>- Validasi `string\|max:2000` di `store`/`update` <br><br>**Yang masih TODO**: kolom `repair_cost_estimate` (decimal) untuk estimasi dana perbaikan/penggantian. Bisa dibuat di migration terpisah jika diperlukan. |
| 4 | Export CSV untuk laporan sarpras ke yayasan | ✅ DONE | Tombol "📥 Export" sudah ditambahkan di page-header `facilities.blade.php` (di sebelah "+ Tambah Barang"), meneruskan query filter aktif via `request()->query()`. CSV kolom baru `repair_cost_estimate`/`repair_notes` akan ditambahkan setelah item Sarpras #3 selesai. |

### 💰 Halaman Riwayat Dana

| # | Item | Status | Catatan |
|---|------|--------|---------|
| 1 | Input manual dana Honda, Bosda, dll (sesuai low-fidelity) — nantinya dipakai untuk honor & sarpras | ⚠️ PARTIAL | Input manual **SUDAH ADA tapi di halaman lain** — di **Dashboard Bendahara** (`/admin/bendahara`), tampil sebagai 9 kartu fund source dengan tombol "Input Dana" per kartu (lihat `BendaharaFundController::store()`, route `admin.bendahara.fund.store`). **Yang BELUM**: tombol input manual di halaman **Riwayat Dana** (`/admin/bendahara/transactions`) — saat ini hanya export & filter periode. <br>Opsi: (a) tambah tombol "+ Catat Transaksi" di Riwayat Dana yang membuka modal sama, atau (b) link "Input Dana" ke Dashboard Bendahara. |

---

## [AKUN SUPER ADMIN]

### 👥 Halaman Manajemen Pengguna

| # | Item | Status | Catatan |
|---|------|--------|---------|
| 1a | Export CSV: kolom "Nama" — untuk role teacher, harusnya tampil nama guru langsung (bukan nama akun) | ✅ DONE | `UserManagementController::export()` di-update: eager-load `teacherDetail` + `parentGuardian`. Untuk role `teacher` ambil dari `teacher_details.name`, untuk role `guest` gabungkan `father_name & mother_name` (atau fallback salah satu). Default `user.name` jika relasi tidak ada. |
| 1b | Export CSV: role belum lengkap — kepala sekolah & orang tua tidak ada, yang ada hanya "guest" | ✅ DONE | Map `$roleLabels` di-tambahkan: `superadmin` → "Super Admin", `headmaster` → "Kepala Sekolah", `administration` → "Administrasi", `bendahara` → "Bendahara", `teacher` → "Guru", `guest` → "Orang Tua". Status juga di-map ke "Aktif"/"Nonaktif". |
| 1c | Export CSV: kolom yang ditampilkan harus sama dengan kolom di tabel halaman (nama, email, telepon, role, status) | ✅ DONE | CSV sudah ada ID, Nama, Email, Phone, Role, Dibuat, Status. Setelah fix 1a/1b, sudah selaras dengan tampilan. |
| 2 | "Tambah Pengguna": password wajib diisi | ✅ DONE | `UserManagementController::store()` baris 80 sudah `'password' => 'required\|string\|min:8\|confirmed'`. Sudah wajib. |

### 🧒 Halaman Data Murid

| # | Item | Status | Catatan |
|---|------|--------|---------|
| 1 | Export CSV: nama orang tua & nomor telepon orang tua belum dicantumkan | ❌ TODO | `StudentManagementController::export()` baris 199–211 hanya ada ID, Nama, Agama, Gender, Grup, Status, Dibuat. **Belum**: kolom `Nama Orang Tua`, `No. Telepon Orang Tua`. Sudah ada eager-loading `with(['parent', 'registration'])` di baris 165 — tinggal akses `$s->parent?->full_name` & `$s->parent?->phone_num` (cek field exactly di model Parent). |
| 2 | Tampilan minus dana kalau dipakai untuk pengeluaran | ❌ TODO | Tidak jelas konteksnya. Apakah ini di halaman Data Murid (tampilan tunggakan/saldo per murid?), atau di halaman Riwayat Dana? Perlu klarifikasi user. Hipotesis: ingin saldo dana sekolah ditampilkan sebagai negatif jika pengeluaran > pemasukan. Dashboard Bendahara sudah punya "Net" yang bisa negatif (defisit). |

---

## Ringkasan

| Modul | DONE | PARTIAL | TODO |
|-------|------|---------|------|
| Registrasi (Guest) | 1 | 0 | 0 |
| Honor Guru | 2 | 0 | 0 |
| Sarpras | 3 | 1 | 0 |
| Riwayat Dana | 0 | 1 | 0 |
| Manajemen Pengguna | 3 | 0 | 0 |
| Data Murid | 0 | 0 | 2 |
| **Total** | **9** | **2** | **2** |

## Prioritas (rekomendasi urutan kerja)

1. ~~**🔴 KRITIS** — Fix bug header tabel Sarpras~~ → ✅ SELESAI
2. ~~**🔴 KRITIS** — Upload Kartu Keluarga di registrasi~~ → ✅ SELESAI
3. ~~**🟠 PENTING** — Tombol Export CSV di halaman Sarpras~~ → ✅ SELESAI
4. ~~**🟠 PENTING** — Map role label & nama guru di export Manajemen Pengguna~~ → ✅ SELESAI
5. ~~**🟡 SEDANG** — Catatan kondisi Sarpras~~ → ✅ SELESAI (kolom estimasi dana masih pending, item terpisah jika diperlukan)
6. **🟡 SEDANG** — Kolom orangtua di export Data Murid (item Student #1).
7. **🟡 SEDANG** — Input manual dana di Riwayat Dana (item Riwayat Dana #1) — utamanya quality-of-life, fungsi sudah ada di Dashboard Bendahara.
8. **🟢 KLARIFIKASI** — Minus dana di Data Murid (item Student #2) — minta detail dulu dari user.
