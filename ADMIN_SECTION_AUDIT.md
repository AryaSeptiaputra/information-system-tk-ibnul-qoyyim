# Admin Sections — Audit & Template

Dokumen ini merangkum **fitur yang sudah ada** pada semua section Superadmin, serta **template/checklist** agar section berikutnya konsisten.

## 1) Inventaris Section yang Sudah Ada
Sumber utama:
- `routes/admin.php`
- `resources/views/components/sidebar.blade.php`

Section Superadmin saat ini:
1. Dashboard (`admin.dashboard`)
2. Manajemen Pengguna (`admin.users.*`)
3. Manajemen Guru (`admin.teachers.*`)
4. Manajemen Pendaftaran (`admin.registrations.*`)
5. Data Orang Tua (`admin.parents.*`)
6. Data Murid (`admin.students.*`)
7. Data Kelas (`admin.classes.*`)
8. Absensi Murid (`admin.student-attendance.*`)
9. Absensi Guru (`admin.teacher-attendance.*`)
10. Tipe Pembayaran (`admin.payment-types.*`)
11. Item Biaya (`admin.fee-items.*`)
12. Pembayaran Murid (`admin.student-payments.*`)

## 2) Pola Arsitektur yang Dipakai (Kontrak Wajib)
### 2.1 List Page (Blade)
Semua section list menggunakan pola:
- `@include('components.dashboard.admin.section-header', [...])`
- `@include('components.dashboard.admin.management-table', [...])`
- `@include('components.dashboard.admin.pagination-controls', [...])`
- `@include('components.dashboard.admin.admin-scripts')`

List page berada di:
- `resources/views/dashboard/admin/*.blade.php`

### 2.2 Modal via AJAX (JSON `{ view: "..." }`)
Flow UI:
- Tombol “Tambah”, “Lihat”, “Edit” memakai atribut `data-modal-open`
- JS (`admin-scripts`) menentukan URL berdasarkan `type`:
  - create: `GET /admin/<endpoint>/create`
  - view: `GET /admin/<endpoint>/{id}`
  - edit: `GET /admin/<endpoint>/{id}/edit`
- Endpoint di controller **harus** return JSON:

```php
return response()->json([
  'view' => view('components.dashboard.admin.modal-form', [...])->render()
]);
```

Submit form di modal:
- Ditangani JS via `fetch()` (delegated) untuk semua `.admin-modal-form`.
- Controller `store/update/destroy` **harus** return JSON `{ success: bool, message: string }`.

### 2.3 Pagination + Preserve Filters
- Controller: `$query->paginate($perPage)->appends($request->query())`
- `pagination-controls` memiliki hidden inputs untuk mempertahankan filter saat `per_page` berubah.

Catatan: sebagian list page hanya menampilkan pagination jika `total() > 0`, sedangkan Attendance menampilkan pagination selalu. Untuk section baru, disarankan **selalu tampilkan** pagination agar UX konsisten.

## 3) Matriks Fitur per Section
Legenda:
- YES: ada
- NO: tidak ada
- LIMITED: ada tapi terbatas/khusus

| Section | Search | Filter | Sort (di controller) | Pagination/per_page | Export | Create | Edit | Delete | Detail Modal | Catatan khusus |
|---|---|---|---|---|---|---|---|---|---|---|
| Users | YES (name/email/phone) | YES role (+status jika kolom ada) | YES | YES | YES (CSV) | YES | YES | YES (blok delete user login) | YES | Status filter cek via `Schema::hasColumn` |
| Teachers | YES (name/phone/education) | YES status (jika kolom ada) | YES | YES | YES (CSV) | YES | YES | YES | YES | Create hanya untuk user role=teacher yang belum punya teacherDetail |
| Registrations | YES (group + JSON candidate/parents) | YES status + group | YES | YES | YES (CSV) | YES | LIMITED status-only (pending → approve/reject) | NO | YES | Detail modal punya form approve/reject + reject_reason |
| Parents | YES (nama/hp/pekerjaan) | YES contact (has/no) | YES | YES | YES (CSV) | YES | YES | NO | YES | Filter contact berbasis field kosong/null |
| Students | YES (name/birth_place/group) | YES status+group+gender | YES | YES | YES (CSV) | YES | YES | NO | YES | Relasi optional ke parents & registrations |
| Classes | YES (class_name/school_year) | NO | YES | YES | YES (CSV) | YES | YES | NO | YES | Detail modal berisi pivot management: attach/detach students & teachers (multi-checkbox) |
| Student Attendance | YES (student name) | YES status + date_from/date_to | YES (date desc) | YES | YES (CSV) | YES | YES | YES | YES | Create mendukung bulk via checkbox list; duplicate per-date di-skip / divalidasi |
| Teacher Attendance | YES (teacher name) | YES status + date_from/date_to | YES (date desc) | YES | YES (CSV) | YES | YES | YES | YES | Bulk via checkbox list; duplicate per-date di-skip / divalidasi |
| Payment Types | YES (code/name) | YES status (active/inactive) | YES | YES | YES (CSV) | YES | YES | NO | YES | Master data tipe pembayaran (dipakai oleh Fee Items & Student Payments) |
| Fee Items | YES (code/name) | YES status + payment_type | YES | YES | YES (CSV) | YES | YES | NO | YES | Master data item biaya (relasi ke Payment Types; periode opsional start/end) |
| Student Payments | YES (murid/tipe/periode/kode unik) | YES status + payment_type + late | YES (created_at desc) | YES | YES (CSV) | YES | YES | NO | YES | Items dibuat otomatis dari Fee Items aktif via `PaymentService` |

## 4) Touch Points Saat Membuat Section Baru
Saat menambah `type` baru (mis. `payment-type`), biasanya perlu perubahan di beberapa tempat:

1) Route group baru di `routes/admin.php` (prefix + name)
2) Controller baru di `app/Http/Controllers/Admin/*ManagementController.php`
3) List page baru di `resources/views/dashboard/admin/<slug>.blade.php`
4) Tambah menu di `resources/views/components/sidebar.blade.php`
5) Update komponen shared agar mengenal `type` baru:
   - `resources/views/components/dashboard/admin/section-header.blade.php` (label + filter UI)
   - `resources/views/components/dashboard/admin/management-table.blade.php` (kolom ringkas + badge)
   - `resources/views/components/dashboard/admin/modal-form.blade.php` (create/edit fields)
   - `resources/views/components/dashboard/admin/modal-detail.blade.php` (read-only detail + action edit/delete bila perlu)
   - `resources/views/components/dashboard/admin/admin-scripts.blade.php`:
     - `getAdminTypeFromModalId()`
     - `getEndpointForAdminType()`
     - `getDeleteItemLabel()`
6) Bila filter baru ditambahkan, `pagination-controls.blade.php` perlu hidden input agar filter tidak hilang saat ganti `per_page`.

## 5) Checklist / Template Implementasi Section Baru
### 5.1 Routes
Minimal pola (sesuaikan kebutuhan):
- `index` (required)
- `create` (required; JSON modal)
- `store` (required; JSON)
- `show` (required; JSON detail)
- `edit` (required; JSON modal)
- `update` (required; JSON)
- `destroy` (optional)
- `export` (optional; disarankan konsisten)

### 5.2 Controller
Wajib ada konsistensi:
- Semua filter dari request berada di `index()`
- Export menerapkan filter yang sama (copy logic atau ekstrak helper private)
- Pagination: `->paginate($perPage)->appends($request->query())`
- Return view list dengan semua nilai filter + `per_page`

Untuk modal endpoints:
- `create()` dan `edit()` return JSON `{ view: ... }`
- `show()` return JSON `{ view: ... }`
- `store/update/destroy` return JSON `{ success, message }`

### 5.3 List View (Blade)
Harus include:
- `section-header` (exportUrl + resetUrl + filter props)
- `management-table` (items)
- `pagination-controls` (items + filter props + per_page)
- `admin-scripts`

### 5.4 Modal Form + Detail
- `modal-form`:
  - Mode create vs edit konsisten (`$action`, `$isEdit`)
  - `@method('PUT')` saat edit
  - Field error pakai `@error()`
- `modal-detail`:
  - Tombol Edit membuka modal edit via `data-modal-open="edit-<type>-modal"`
  - Delete hanya jika route destroy ada (dan JS mapping sudah diaktifkan)

### 5.5 JS Mapping
- Pastikan `data-modal-open` id mengandung token unik untuk type.
- Tambahkan mapping endpoint agar URL terbentuk benar.

### 5.6 Export
Saat ini label UI “Download Excel” tetapi output yang dihasilkan adalah CSV.
- Jika tetap CSV: biarkan, tapi catat ini.
- Jika ingin Excel asli: perlu library (mis. Laravel Excel) — bukan bagian dari pola existing saat ini.

## 6) Rekomendasi Urutan Section Berikutnya (Finance)
Berdasarkan schema/model yang sudah ada, urutan paling aman agar bertahap dan reusable:
✅ 1) Payment Types (`payment_types`) — master data sederhana
✅ 2) Fee Items (`fee_items`) — master data biaya
✅ 3) Student Payments (`student_payments` + `student_payment_items`) — transaksi + detail
➡️ 4) Teacher Honor (`teacher_honors`) — honorarium

Alasan:
- (1) dan (2) mudah (CRUD standar) → bagus untuk memastikan template checklist berjalan.
- (3) butuh relasi & detail view yang lebih kompleks (mirip pola pivot di kelas).

## 7) Catatan Konsistensi yang Perlu Diperhatikan
- Pagination: sebagian section hanya render jika `total() > 0`; attendance render selalu.
- Sorting: beberapa controller menerima `sort/order`, tetapi belum ada kontrol UI pada list page.
- `showDelete` ada di `management-table`, tapi saat ini aksi delete dilakukan dari **detail modal**, bukan dari table.

---

Jika kamu setuju, langkah berikutnya: kita mulai dari **Payment Types** sebagai section baru pertama (mengikuti checklist ini), lalu lanjut Fee Items.

Update terbaru: Payment Types, Fee Items, dan Student Payments sudah selesai. Section finance berikutnya yang paling natural untuk dibangun adalah **Teacher Honor**.
