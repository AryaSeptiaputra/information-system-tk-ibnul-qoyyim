<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mapping Komponen Pembayaran → Fund Source
    |--------------------------------------------------------------------------
    | Saat student_payment lunas, sistem loop tiap komponen pada
    | detail_fee_template dan kredit nominal ke fund_source sesuai mapping.
    | Komponen yang label-nya TIDAK match mapping di sini → masuk ke
    | source 'lain_lain' (fallback) agar tidak ada uang yang hilang.
    |
    | Untuk jenis_payment monolitik (mis. iuran_bulanan / uang_pendaftaran)
    | yang tidak punya komponen detail, mapping diambil dari
    | jenis_payment_mapping di bawah.
    */

    'component_mapping' => [
        // label komponen (case-insensitive, dicocokkan via mb_strtolower) → fund_source code
        'uang pembangunan' => 'gedung',
        'seragam'          => 'seragam',
        'atk'              => 'atk',
        'sarana'           => 'sarana',
        // 'buku paket' dan 'sampul rapor' sengaja tidak dipetakan → masuk 'lain_lain'.
    ],

    'jenis_payment_mapping' => [
        'iuran_bulanan'    => 'spp',
        // 'uang_pendaftaran' tidak dipetakan default; bila perlu auto-credit, isi di sini.
    ],

    'fallback_source' => 'lain_lain',
];
