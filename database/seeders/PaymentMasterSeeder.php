<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentMasterSeeder extends Seeder
{
    public function run(): void
    {
        $amount = (float) env('UANG_PENDAFTARAN_AMOUNT', 0);
        $active = (bool) env('UANG_PENDAFTARAN_ACTIVE', false);

        $template = null;
        if ($amount > 0) {
            $template = [
                [
                    'label' => 'Uang Pendaftaran',
                    'amount' => $amount,
                    'qty' => 1,
                ],
            ];
        }

        Payment::query()->updateOrCreate(
            ['jenis_payment' => 'uang_pendaftaran'],
            [
                'name' => 'Uang Pendaftaran',
                'period_mode' => 'one_time',
                'detail_fee_template' => $template,
                'default_amount' => $amount,
                'is_active' => $active,
                'start_date' => null,
                'end_date' => null,
            ]
        );

        // Iuran Masuk: 6 komponen mengikuti template Excel FORMAT PEMBAYARAN IURAN SISWA
        // + komponen ATK & Sarana untuk auto-credit ke fund_sources card Bendahara.
        // firstOrCreate dipakai agar nominal yang sudah diisi admin via UI tidak ter-overwrite saat re-seed.
        // Mapping komponen → fund_source diatur di config/fund_sources.php.
        Payment::query()->firstOrCreate(
            ['jenis_payment' => 'iuran_masuk'],
            [
                'name' => 'Iuran Masuk',
                'period_mode' => 'one_time',
                'detail_fee_template' => [
                    ['label' => 'Uang Pembangunan', 'amount' => 0, 'qty' => 1],
                    ['label' => 'Seragam',          'amount' => 0, 'qty' => 1],
                    ['label' => 'ATK',              'amount' => 0, 'qty' => 1],
                    ['label' => 'Sarana',           'amount' => 0, 'qty' => 1],
                    ['label' => 'Buku Paket',       'amount' => 0, 'qty' => 1],
                    ['label' => 'Sampul Rapor',     'amount' => 0, 'qty' => 1],
                ],
                'default_amount' => 0,
                'is_active' => true,
                'start_date' => null,
                'end_date' => null,
            ]
        );

        // Iuran Bulanan: 1 komponen, period_mode=monthly. Admin isi nominal via UI.
        Payment::query()->firstOrCreate(
            ['jenis_payment' => 'iuran_bulanan'],
            [
                'name' => 'Iuran Bulanan',
                'period_mode' => 'monthly',
                'detail_fee_template' => [
                    ['label' => 'Iuran Bulanan', 'amount' => 0, 'qty' => 1],
                ],
                'default_amount' => 0,
                'is_active' => true,
                'start_date' => null,
                'end_date' => null,
            ]
        );
    }
}
