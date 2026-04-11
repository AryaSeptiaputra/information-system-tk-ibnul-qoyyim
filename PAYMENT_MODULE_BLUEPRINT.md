# Payment Module Blueprint (Callback Notes)

Tanggal: 2026-04-08

Dokumen ini menyimpan **rancangan DB + rancangan modul management pembayaran** yang sebelumnya pernah kita bangun/diskusikan, supaya bisa dipakai lagi saat payment module diaktifkan ulang.

> Catatan penting: saat ini fitur payment sudah di-reset dan tabel-tabelnya sudah di-drop lewat migration drop. Dokumen ini adalah **blueprint**, bukan status implementasi aktif.

## 1) Ruang Lingkup Modul (Admin)
Section yang dulu ada di sidebar admin:
- **Payment Types** (`admin.payment-types.*`) — master data
- **Fee Items** (`admin.fee-items.*`) — master data biaya per tipe pembayaran
- **Student Payments** (`admin.student-payments.*`) — transaksi pembayaran per murid (berisi item detail + opsional angsuran)

Kontrak UI admin (pola wajib untuk semua section):
- List page memakai komponen: `section-header`, `management-table`, `pagination-controls`, `admin-scripts`
- Create/Edit/Detail memakai **AJAX modal** (endpoint controller return JSON `{ view: "..." }`)
- Submit modal via `fetch()` dan response JSON `{ success, message }`

Referensi pola lengkap: `ADMIN_SECTION_AUDIT.md`.

## 2) Rancangan Database (Skema Inti)

### 2.1 `payment_types`
Tujuan: master data tipe pembayaran.

Kolom (berdasarkan migrasi):
- `id_payment_type` (PK)
- `code` (unique)
- `name`
- `description` (nullable)
- `is_active` (boolean, default true)
- timestamps

### 2.2 `fee_items`
Tujuan: master data item biaya yang terikat ke satu `payment_type`.

Relasi:
- `fee_items.id_payment_type` → `payment_types.id_payment_type` (cascade)

Kolom:
- `id_fee_item` (PK)
- `id_payment_type` (FK)
- `code` (unique)
- `name`
- `description` (nullable)
- `default_amount` (decimal 12,2)
- `is_active` (boolean, default true)
- `start_date` (nullable)
- `end_date` (nullable)
- timestamps

Catatan: `start_date/end_date` dipakai untuk “masa berlaku” item biaya (opsional).

### 2.3 `student_payments`
Tujuan: header transaksi pembayaran murid.

Relasi:
- `student_payments.id_student` → `students.id_student` (cascade)
- `student_payments.id_payment_type` → `payment_types.id_payment_type` (cascade)

Kolom:
- `id_student_payment` (PK)
- `id_student` (FK)
- `id_payment_type` (FK)
- `payment_period` (string, nullable) — contoh: `2026-07` atau `2026/2027`

Ringkasan angka:
- `total_amount` (decimal 12,2)
- `discount_amount` (decimal 12,2)
- `final_amount` (decimal 12,2)

Metode & status:
- `payment_method` enum: `transfer_bank | e_wallet | cash | qris` (nullable)
- `status` enum: `pending | paid | failed` (default `pending`)

Bukti & metadata:
- `is_late` (boolean, default false)
- `unique_code` (nullable)
- `unique_code_valid_until` (nullable datetime)
- `proof_file` (nullable string)
- `paid_at` (nullable datetime)

Angsuran:
- `installment_requested` (boolean, default false)
- `installment_count` (nullable tinyint)

Index:
- index: (`id_student`, `id_payment_type`)

### 2.4 `student_payment_items`
Tujuan: detail item yang membentuk satu `student_payment` (snapshot dari `fee_items` saat dibuat).

Relasi:
- `student_payment_items.id_student_payment` → `student_payments.id_student_payment` (cascade)
- `student_payment_items.id_fee_item` → `fee_items.id_fee_item` (nullable, nullOnDelete)

Kolom:
- `id_student_payment_item` (PK)
- `id_student_payment` (FK)
- `id_fee_item` (nullable FK)
- `item_code` (nullable)
- `item_name`
- `description` (nullable)
- `quantity` (uint, default 1)
- `unit_price` (decimal 12,2)
- `discount` (decimal 12,2)
- `subtotal` (decimal 12,2)
- timestamps

### 2.5 `student_payment_installments`
Tujuan: jadwal & tracking angsuran untuk satu `student_payment`.

Relasi:
- `student_payment_installments.id_student_payment` → `student_payments.id_student_payment` (cascade)

Kolom:
- `id_student_payment_installment` (PK)
- `id_student_payment` (FK)
- `installment_number` (uint)
- `due_date` (date)
- `installment_amount` (decimal 12,2)
- `status` enum: `pending | paid` (default `pending`)
- `paid_at` (nullable datetime)
- `payment_method` enum: `transfer_bank | e_wallet | cash | qris` (nullable)
- `proof_file` (nullable string)
- timestamps

Constraints + index:
- unique: (`id_student_payment`, `installment_number`)
- index: (`id_student_payment`, `status`)
- index: (`due_date`)

## 3) Rancangan Logic / Flow (High Level)

### 3.1 Master data (Payment Types → Fee Items)
- Admin membuat **Payment Type** (kode + nama, aktif/nonaktif)
- Admin membuat **Fee Item** yang terikat ke Payment Type
- Fee Item bisa dibatasi periode berlaku (start/end) dan bisa dinonaktifkan

### 3.2 Membuat Student Payment
- Admin memilih `student` + `payment_type` (+ `payment_period` bila dipakai)
- Sistem membuat `student_payments` (header)
- Sistem meng-generate `student_payment_items` dari Fee Items yang relevan (umumnya: fee items aktif untuk payment type tsb)
- Sistem hitung `total_amount`, `discount_amount`, `final_amount`

Catatan historis: di implementasi sebelumnya, pembuatan item ini dicatat sebagai dibuat otomatis via `PaymentService` (service layer).

### 3.3 Pembayaran & bukti
- Status dasar: `pending` → `paid` (atau `failed`)
- Bukti bayar disimpan via `proof_file` (string path)
- `paid_at` mencatat waktu pelunasan

### 3.4 Angsuran
- `installment_requested` + `installment_count` pada `student_payments` menandakan kebutuhan angsuran
- Detail angsuran disimpan di `student_payment_installments` (tiap installment punya `due_date`, `status`, dan bukti)

## 4) Catatan / Keputusan yang Perlu Dipastikan Saat Rebuild
Ini hal-hal yang *belum terkunci* di skema saat ini dan perlu kita putuskan saat mulai implement ulang:
- Definisi & sumber `is_late` (berdasarkan `due_date` angsuran? deadline per payment? manual flag?)
- Strategi `unique_code` (kapan dibuat, aturan masa berlaku, apakah mempengaruhi `final_amount`)
- Aturan “fee items relevan” saat generate payment (filter `start_date/end_date` vs tanggal sekarang vs `payment_period`)
- Alur request/approve angsuran (butuh status tambahan atau cukup boolean sekarang)
- Lokasi penyimpanan file bukti (disk `public`/`local`, folder naming)

## 5) Referensi Sumber (di repo)
- Dokumentasi pola section admin: `ADMIN_SECTION_AUDIT.md`
- Skema (migrasi historis):
  - `database/migrations/2026_04_04_000003_000000_create_payment_types_table.php`
  - `database/migrations/2026_04_04_000004_000000_create_fee_items_table.php`
  - `database/migrations/2026_04_04_000006_000000_create_student_payments_table.php`
  - `database/migrations/2026_04_04_000007_000000_create_student_payment_items_table.php`
  - `database/migrations/2026_04_08_000000_add_installment_fields_and_create_student_payment_installments_table.php`
  - `database/migrations/2026_04_08_000001_add_proof_file_to_student_payment_installments_table.php`

