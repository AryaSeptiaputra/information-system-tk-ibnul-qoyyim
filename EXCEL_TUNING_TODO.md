# TODO — Tuning Sistem Agar Nyambung dengan Template Excel

Tanggal: 2026-05-05

Tujuan dokumen ini: menjadi checklist kerja bertahap untuk menyesuaikan sistem (DB + UI + alur kerja) agar **bisa mengisi data** dan **menghasilkan laporan** yang sejalan dengan file Excel yang diberikan, tanpa merusak desain DB yang sudah benar.

## Prinsip Keputusan
1. **DB tetap “source of truth”** (normalized). Excel diperlakukan sebagai **format input/output/report**, bukan struktur tabel.
2. Yang dituning di DB hanya:
   - field yang memang dibutuhkan operasional,
   - field yang sering muncul di template Excel,
   - dan tidak membuat tabel/kolom “pivot” seperti kolom 1..31.
3. Fitur berat/berisiko (pivot absensi & laporan bulanan multi-sheet) diposisikan sebagai **fase lanjutan**.

## Input Excel yang Diaudit
Folder: `storage/app/excel_audit/`
- `data siswa TA. 2025-2026.xlsx`
- `DATA GURU TERBARU 2025.xlsx`
- `DAFTAR HADIR GURU TERBARU.xlsx`
- `ABSENSI SEMUA KELAS.xlsx`
- `DATA SARANA.xlsx`
- `FORMAT PEMBAYARAN IURAN SISWA.xlsx`
- `LAPORAN BULANAN AGUSTUS 2025.xlsx`

Referensi hasil audit: `storage/app/excel_audit/report.json`

---

## Milestone 0 — Validasi Baseline (Wajib)
- [ ] Pastikan migrasi jalan dan schema terkini benar (migrations sebagai source-of-truth)
- [ ] Jalankan ulang audit bila ada perubahan schema:
  - `python scripts/excel_audit.py --excel-dir storage/app/excel_audit --out storage/app/excel_audit/report.json --migrations-dir database/migrations`
  - `python scripts/analyze_audit_report.py`
- [ ] Tetapkan target scope (MVP vs Lanjutan) sebelum coding

---

## Milestone 1 (MVP, realistis) — Tutup Gap Field Master Data

### 1A. Siswa & Orang Tua (Excel: data siswa TA)
**Temuan audit:** cocok tinggi, gap utama: `AGAMA` dan detail pekerjaan ortu.

DB saat ini:
- `students`: `name, birth_place, birth_date, gender, group, status, id_parents, ...`
- `parents`: sudah ada `father_occupation`, `mother_occupation`

TODO:
- [ ] Tambah kolom `students.religion` (nullable)
- [ ] (Opsional) Tambah `students.address` jika ke depannya dibutuhkan (tidak ada di header audit saat ini)
- [ ] Review mapping “PEKERJAAN ORANG TUA”:
  - opsi A: isi ke `father_occupation` **dan** `mother_occupation` (jika hanya 1 kolom di Excel)
  - opsi B: buat kolom `parents.guardian_occupation` (jika ingin 1 sumber)
- [ ] Update form/UI siswa agar bisa input `religion` (dan pekerjaan ortu sesuai keputusan)
- [ ] Pastikan tampilan list/detail siswa menampilkan field baru

**Mapping kolom Excel → DB (disepakati saat eksekusi):**
- `NAMA ANAK` → `students.name`
- `TEMPAT LAHIR` → `students.birth_place`
- `TANGGAL LAHIR` → `students.birth_date`
- `JENIS KELAMIN` → `students.gender`
- `AGAMA` → `students.religion` (baru)
- `NAMA AYAH` → `parents.father_name`
- `NAMA IBU` → `parents.mother_name`
- `PEKERJAAN ORANG TUA` → keputusan (lihat TODO di atas)

---

### 1B. Guru (Excel: DATA GURU TERBARU 2025)
**Temuan audit:** cocok sedang-tinggi; gap utama: jabatan, NIP/NUPTK, TTL, masa kerja.

DB saat ini:
- `teacher_details`: `name, education, phone_num, email, status, id_user`

TODO (schema + UI):
- [ ] Tambah kolom di `teacher_details`:
  - [ ] `position` (jabatan) nullable
  - [ ] `nip` nullable
  - [ ] `nuptk` nullable
  - [ ] `birth_place` nullable
  - [ ] `birth_date` nullable (date)
  - [ ] `start_work_date` nullable (date) untuk derivasi “masa kerja”
- [ ] Tentukan aturan parsing kolom `TEMPAT TANGGAL LAHIR` (Excel campur tempat+tanggal)
- [ ] Update form/UI guru agar input field-field di atas
- [ ] Update halaman profil guru (termasuk “Honor saya” yang sudah ada) agar menampilkan profil lengkap

**Mapping kolom Excel → DB:**
- `NAMA LENGKAP DAN GELAR` → `teacher_details.name`
- `NO. HP` → `teacher_details.phone_num`
- `JABATAN` → `teacher_details.position`
- `NIP/NUPTK` → split ke `teacher_details.nip` / `teacher_details.nuptk`
- `TEMPAT TANGGAL LAHIR` → parse ke `birth_place` + `birth_date`
- `Masa Kerja` → (lebih baik) dihasilkan dari `start_work_date`

---

### 1C. Sarana/Inventaris (Excel: DATA SARANA)
**Temuan audit:** tabel `facilities` ada, tapi Excel butuh `SUMBER DANA` + `TAHUN`.

DB saat ini:
- `facilities`: `name, description, quantity, condition, image_path, is_active`

TODO:
- [ ] Tambah kolom `facilities.fund_source` (string, nullable)
- [ ] Tambah kolom `facilities.acquisition_year` (unsignedSmallInteger/int, nullable)
- [ ] (Opsional) tambah `facilities.category` bila dibutuhkan nanti
- [ ] Update form/UI sarana agar input field baru

**Mapping kolom Excel → DB:**
- `SARANA/BARANG` → `facilities.name`
- `JUMLAH` → `facilities.quantity`
- `KONDISI` → `facilities.condition`
- `SUMBER DANA` → `facilities.fund_source` (baru)
- `TAHUN` → `facilities.acquisition_year` (baru)

---

## Milestone 2 (MVP) — Selaraskan Pembayaran dengan “FORMAT PEMBAYARAN IURAN SISWA”
**Catatan:** DB pembayaran sudah fleksibel (template JSON + snapshot). Yang diperlukan adalah konfigurasi + UX yang jelas.

TODO:
- [ ] Pastikan struktur komponen biaya disimpan di `payments.detail_fee_template`
- [ ] Tetapkan standar penamaan komponen agar match dengan header Excel:
  - contoh: `UANG PEMBANGUNAN`, `SERAGAM`, `BUKU PAKET`, `SAMPUL RAPOR`
- [ ] Tambahkan/rapikan UI untuk:
  - membuat “jenis pembayaran” dengan komponen
  - mencatat pembayaran siswa per periode
- [ ] Tentukan definisi `payment_period` (bulan/semester/tahun) agar export bisa rapi

Output MVP yang realistis:
- [ ] Bisa menghasilkan rekap pembayaran per siswa per jenis pembayaran
- [ ] (Opsional) export sederhana ke Excel/CSV per siswa

---

## Milestone 3 (LANJUTAN, berat) — Absensi Matriks (Excel: ABSENSI SEMUA KELAS)
**Temuan audit:** sheet bertipe `pivot:days` (kolom 1..31) ⇒ harus transform ke row-based.

DB sudah benar:
- `student_attendance(id_student, date, status, information)`

TODO desain (pilih salah satu pendekatan):
- [ ] A. **Tanpa import Excel**: input absensi harian via UI (paling murah)
- [ ] B. **Import Excel matriks**: buat transform “kolom hari → baris”
  - [ ] Definisikan mapping status kolom harian (mis. `H/S/I/A`) ke `status`
  - [ ] Definisikan bulan/tahun konteks (di Excel biasanya implicit)
  - [ ] Cocokkan “nama siswa” di Excel dengan `students.name` (rawan mismatch → perlu ID)

Risiko utama:
- mismatch nama
- bulan/tahun tidak eksplisit
- format tiap sheet bisa beda

---

## Milestone 4 (LANJUTAN, berat) — Laporan Bulanan Multi-Sheet
Excel: `LAPORAN BULANAN AGUSTUS 2025.xlsx`

TODO:
- [ ] Identifikasi sheet yang hanya “profil/master” vs “laporan agregat”
- [ ] Tentukan target:
  - [ ] hanya export data (CSV/Excel) dari DB
  - [ ] atau generate layout laporan yang sama persis (butuh template engine/report builder)

Catatan:
- Menyamakan layout laporan biasanya lebih mahal daripada menambah field DB.

---

## Definisi “Selesai” untuk MVP
- [ ] Field `AGAMA` siswa tersimpan dan tampil
- [ ] Profil guru memiliki `jabatan`, `nip/nuptk`, TTL, dan masa kerja dapat ditampilkan (berbasis `start_work_date`)
- [ ] Sarana memiliki `sumber dana` dan `tahun perolehan`
- [ ] Pembayaran: komponen biaya bisa dikonfigurasi dan direkap

## Catatan Scope vs Budget 800rb
Rekomendasi realistis untuk 800rb:
- Fokus Milestone 1 + sebagian Milestone 2.
- Milestone 3–4 sebaiknya dijadikan “fase lanjutan” karena butuh waktu analisis format dan risiko mismatch.
