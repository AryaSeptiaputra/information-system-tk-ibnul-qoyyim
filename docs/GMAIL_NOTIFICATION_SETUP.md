# Setup Notifikasi Email via Gmail (SMTP)

Dokumen ini menjelaskan cara mengaktifkan notifikasi email ketika tagihan murid dibuat. Sistem saat ini mengirim email menggunakan Gmail SMTP.

## Ringkas Fitur
- Email terkirim saat tagihan murid dibuat.
- Template email: resources/views/emails/student-payment-created.blade.php.
- Mailable: app/Mail/StudentPaymentCreated.php.
- Trigger: app/Http/Controllers/Admin/StudentPaymentManagementController.php.

## Prasyarat
- Akun Gmail yang akan dipakai sebagai pengirim.
- Gmail sudah mengaktifkan 2-Step Verification.
- App Password Gmail (bukan password utama).

## Langkah Konfigurasi Gmail
1. Aktifkan 2-Step Verification di akun Gmail.
2. Buat App Password:
   - Google Account -> Security -> App passwords
   - Pilih app dan device, lalu generate password
   - Simpan 16-digit App Password

## Konfigurasi .env
Tambahkan atau sesuaikan nilai berikut di file .env:

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_gmail_address@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_gmail_address@gmail.com
MAIL_FROM_NAME="TK Ibnul Qoyyim"

APP_URL=https://your-domain.com

Catatan:
- MAIL_PASSWORD harus menggunakan App Password.
- MAIL_FROM_ADDRESS sebaiknya sama dengan MAIL_USERNAME untuk menghindari masalah pengiriman.
- APP_URL dipakai untuk generate link di email (menu Tagihan).

## Uji Kirim Email (Opsional)
Jika ingin uji cepat, jalankan di tinker:

php artisan tinker

Lalu:

$user = \App\Models\User::where('email', 'your_gmail_address@gmail.com')->first();
$studentPayment = \App\Models\StudentPayment::query()->with(['student','payment'])->latest('id_student_payment')->first();
Mail::to($user->email)->send(new \App\Mail\StudentPaymentCreated($studentPayment, $user));

## Penyesuaian Template
- File template email: resources/views/emails/student-payment-created.blade.php
- Ubah teks, gaya, atau tambahan info sesuai kebutuhan.

## Troubleshooting Singkat
- Email tidak terkirim:
  - Pastikan MAIL_USERNAME/PASSWORD benar
  - Pastikan App Password valid
  - Cek log: storage/logs/laravel.log
- Link di email salah:
  - Pastikan APP_URL sesuai domain
