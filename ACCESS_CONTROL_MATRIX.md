# Access Control Matrix (Role-Based)

Dokumen ini adalah audit *source-of-truth* akses aplikasi berdasarkan konfigurasi **routes + middleware + guard di controller**.

## Roles
- `guest` — Orang tua/wali murid (portal `/dashboard`)
- `teacher` — Guru (portal staff `/admin` untuk data akademik & absensi)
- `administration` — Tata Usaha / Administrasi (portal staff `/admin`, termasuk manajemen operasional & keuangan)
- `headmaster` — Kepala sekolah (portal staff `/admin`, mayoritas *view-only*)
- `superadmin` — Admin sistem (akses penuh + manajemen pengguna)

## Source of Truth
- Staff portal routes: [routes/admin.php](routes/admin.php)
- Guest & shared authenticated routes: [routes/web.php](routes/web.php)
- Role middleware: [app/Http/Middleware/EnsureRole.php](app/Http/Middleware/EnsureRole.php)
- Superadmin-only middleware: [app/Http/Middleware/EnsureSuperAdmin.php](app/Http/Middleware/EnsureSuperAdmin.php)
- Sidebar exposure (UX): [resources/views/components/sidebar.blade.php](resources/views/components/sidebar.blade.php)

## Definitions
- **View**: `index`, `show`, `export`, halaman detail, statistik, dll.
- **Manage**: `create`, `store`, `edit`, `update`, `destroy`, serta aksi mutasi seperti `approve/reject`, `attach/detach`, `reset`, `pay`, dll.

Legend:
- ✅ = allowed
- 👁️ = view-only
- ✍️ = manage (create/update/delete/mutate)
- ❌ = denied (403 / redirect)

---

## A. Staff Portal `/admin` (Route-level enforced)
Semua route `/admin/*` wajib memenuhi `auth` dan `ensure.role:superadmin,administration,teacher,headmaster`.

### Module Access Matrix (Admin)
| Module (Admin) | superadmin | administration | headmaster | teacher |
|---|---:|---:|---:|---:|
| Admin Dashboard (`GET /admin`) | ✅ | ✅ | ✅ | ✅ |
| User Management (`/admin/users/*`) | ✍️ | ❌ | ❌ | ❌ |
| Teachers (`/admin/teachers/*`) | ✍️ | ✍️ | 👁️ | ❌ |
| Registrations (`/admin/registrations/*`) | ✍️ | ✍️ | 👁️ | ❌ |
| Parents/Guardians (`/admin/parents/*`) | ✍️ | ✍️ | 👁️ | ❌ |
| Students (`/admin/students/*`) | ✍️ | ✍️ | 👁️ | 👁️ |
| Classes (`/admin/classes/*`) | ✍️ | ✍️ | 👁️ | 👁️ |
| Student Attendance (`/admin/student-attendance/*`) | ✍️ | ✍️ | 👁️ | ✍️ |
| Teacher Attendance (`/admin/teacher-attendance/*`) | ✍️ | ✍️ | 👁️ | ✍️ |
| Teacher Honors (`/admin/teacher-honors/*`) | ✍️ | ✍️ | 👁️ | ❌ |
| Honor Saya (Self) (`/admin/my-honor`) | ❌ | ❌ | ❌ | 👁️ |
| Facilities/Sarpras (`/admin/facilities/*`) | ✍️ | ✍️ | 👁️ | ❌ |
| Payments (Master) (`/admin/payments/*`) | ✍️ | ✍️ | 👁️ | ❌ |
| Student Payments (Transactions) (`/admin/student-payments/*`) | ✍️ | ✍️ | 👁️ | ❌ |
| Settings: Payment Info (`/admin/settings/payment-info`) | ✍️ | ✍️ | 👁️ | ❌ |

### Notes per Module (Admin)
- **User Management**
  - Dilindungi `ensure.super.admin` (superadmin saja), termasuk create/update/delete/export.
- **Teacher / Registration / Parent / Honor / Facilities / Payments / Student-Payments**
  - Pola konsisten: `headmaster` hanya route **view**.
  - `administration` & `superadmin` punya **manage**.
  - `teacher` umumnya tidak punya akses modul-modul administrasi/keuangan.
- **Students & Classes**
  - `teacher` memiliki **view**.
  - Aksi pivot `attach/detach` student/teacher ke kelas hanya untuk `superadmin, administration`.
- **Attendance (Student & Teacher)**
  - `teacher` bisa **manage** (create/update/delete). `headmaster` hanya **view**.
- **Settings: Payment Info**
  - Route `GET edit` bisa diakses `headmaster`, namun route mutasi (`PUT/POST/DELETE`) hanya `superadmin, administration`.
- **Honor Saya (Self)**
  - Route `GET /admin/my-honor` hanya untuk role `teacher` (read-only, data guru yang terkait dengan akun login).

---

## B. Guest Portal `/dashboard` (Controller-level enforced)
Route berikut hanya diproteksi `auth` di [routes/web.php](routes/web.php), bukan `ensure.role`.

### Guest Features
| Feature (Dashboard) | guest | teacher | administration | headmaster | superadmin |
|---|---:|---:|---:|---:|---:|
| Dashboard home (`GET /dashboard`) | ✅ | ➜ `/admin` | ➜ `/admin` | ➜ `/admin` | ➜ `/admin` |
| Info (`GET /dashboard/info`) | ✅ | ❌*** | ❌*** | ❌*** | ❌*** |
| Bills (`GET /dashboard/bills`) | ✅ | ❌*** | ❌*** | ❌*** | ❌*** |
| Upload proof (full payment) (`POST /dashboard/bills/{studentPayment}/pay`) | ✍️ | ❌*** | ❌*** | ❌*** | ❌*** |
| Upload proof (installment) (`POST /dashboard/bills/{studentPayment}/installments/{installment}/pay`) | ✍️ | ❌*** | ❌*** | ❌*** | ❌*** |

Keterangan:
- Staff roles (`teacher`, `administration`, `headmaster`, `superadmin`) akan di-*redirect* ke `/admin` di `DashboardController@index`.
- `***` Halaman guest detail (`/dashboard/info`, `/dashboard/bills`, dan aksi upload bukti) melakukan guard: jika role bukan `guest` maka redirect ke `/admin`.

### Registration (Authenticated)
| Feature (Registration) | guest | teacher | administration | headmaster | superadmin |
|---|---:|---:|---:|---:|---:|
| Create (`GET /registration/create`) | ✅ | ✅* | ✅* | ✅* | ✅* |
| Store (`POST /registration`) | ✍️ | ✍️* | ✍️* | ✍️* | ✍️* |
| Show own (`GET /registration/{registration}`) | ✅ | ✅* | ✅* | ✅* | ✅* |

Catatan:
- `RegistrationController` hanya memastikan `auth` + ownership (`id_user`), tidak membatasi role.
- Jika pendaftaran seharusnya khusus `guest`, pertimbangkan menambahkan pembatasan role di route atau controller.

---

## C. UI/UX Exposure vs Security
- Security **tetap** berada di level route middleware (`ensure.role`, `ensure.super.admin`) dan guard controller.
- Beberapa komponen Blade menyembunyikan tombol mutasi agar role view-only tidak melihat aksi yang akan berujung 403.
  - Sidebar berbasis role ada di [resources/views/components/sidebar.blade.php](resources/views/components/sidebar.blade.php).

---

## Recommendations (Optional)
1. Jika ingin *hard separation* antara portal staff dan guest:
   - Tambahkan role guard pada route `/dashboard/*` dan `/registration/*` (misalnya `ensure.role:guest`).
2. Redirect role staff dari `/dashboard` ke `/admin` sudah diterapkan di `DashboardController`.
