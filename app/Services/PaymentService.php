<?php

namespace App\Services;

use App\Models\PaymentType;
use App\Models\FeeItem;
use App\Models\StudentPayment;
use App\Models\StudentPaymentItem;

class PaymentService
{
    /**
     * Generate fee breakdown for a payment type
     */
    public function generateFeeBreakdown(PaymentType $paymentType): array
    {
        $items = FeeItem::where('id_payment_type', $paymentType->id_payment_type)
            ->where('is_active', true)
            ->get();

        $breakdown = [
            'items' => [],
            'total_amount' => 0,
            'discount_amount' => 0,
            'final_amount' => 0,
        ];

        foreach ($items as $item) {
            $breakdown['items'][] = [
                'id_fee_item' => $item->id_fee_item,
                'code' => $item->code,
                'name' => $item->name,
                'quantity' => 1,
                'unit_price' => (float)$item->default_amount,
                'subtotal' => (float)$item->default_amount,
            ];
            $breakdown['total_amount'] += (float)$item->default_amount;
        }

        $breakdown['final_amount'] = $breakdown['total_amount'] - $breakdown['discount_amount'];

        return $breakdown;
    }

    /**
     * Create payment with items
     */
    public function createPaymentWithItems(StudentPayment $payment): void
    {
        if ($payment->items()->count() > 0) {
            return; // Already has items
        }

        $paymentType = $payment->paymentType;
        $feeItems = FeeItem::where('id_payment_type', $paymentType->id_payment_type)
            ->where('is_active', true)
            ->get();

        $totalAmount = 0;

        foreach ($feeItems as $feeItem) {
            $unitPrice = (float)$feeItem->default_amount;
            $subtotal = $unitPrice; // quantity = 1

            StudentPaymentItem::create([
                'id_student_payment' => $payment->id_student_payment,
                'id_fee_item' => $feeItem->id_fee_item,
                'item_code' => $feeItem->code,
                'item_name' => $feeItem->name,
                'description' => $feeItem->description,
                'quantity' => 1,
                'unit_price' => $unitPrice,
                'discount' => 0,
                'subtotal' => $subtotal,
            ]);

            $totalAmount += $subtotal;
        }

        // Update payment totals
        $payment->total_amount = $totalAmount;
        $payment->discount_amount = 0;
        $payment->final_amount = $totalAmount;
        $payment->save();
    }

    /**
     * Generate unique code for bank transfer (+Rp 150)
     * Returns a random 3-digit number
     */
    public function generateUniqueCode(): string
    {
        return str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get all available payment methods
     */
    public function getPaymentMethods(): array
    {
        return [
            [
                'code' => 'transfer_bank',
                'name' => 'Transfer Bank',
                'description' => 'Transfer ke rekening sekolah',
                'info' => [
                    'bank_name' => 'BRI',
                    'account_number' => '1234567890',
                    'account_name' => 'TK Ibnul Qoyyim',
                    'note' => 'Gunakan kode unik di akhir nominal transfer untuk konfirmasi otomatis',
                ],
            ],
            [
                'code' => 'e_wallet',
                'name' => 'E-Wallet',
                'description' => 'GCash / OVO',
                'info' => [
                    'phone_number' => '+62-812-3456-7890',
                    'qr_code' => '/images/qr-codes/ewallet-qr.png',
                ],
            ],
            [
                'code' => 'cash',
                'name' => 'Tunai',
                'description' => 'Bayar langsung ke sekolah',
                'info' => [
                    'phone_number' => '+62-812-3456-7890',
                    'address' => 'TK Ibnul Qoyyim, Jl. Pendidikan No. 1, Bandung',
                    'pic_name' => 'Ibu Siti (Admin)',
                    'office_hours' => '08:00 - 15:30 (Senin-Jumat)',
                ],
            ],
            [
                'code' => 'qris',
                'name' => 'QRIS',
                'description' => 'Scan QRIS',
                'info' => [
                    'qr_code' => '/images/qr-codes/qris-code.png',
                    'note' => 'Scan kode QR untuk transaksi otomatis',
                ],
            ],
        ];
    }

    /**
     * Mark payment as late
     */
    public function markAsLate(StudentPayment $payment): void
    {
        $payment->is_late = true;
        $payment->save();

        if ($payment->student) {
            $payment->student->paid_late = true;
            $payment->student->save();
        }
    }

    /**
     * Check if payment is overdue
     */
    public function isOverdue(StudentPayment $payment): bool
    {
        $registration = $payment->student?->registration;
        return $registration && now() > $registration->grace_period_until;
    }

    /**
     * Check if in grace period
     */
    public function inGracePeriod(StudentPayment $payment): bool
    {
        $registration = $payment->student?->registration;
        return $registration && 
               now() > $registration->payment_deadline && 
               now() <= $registration->grace_period_until;
    }
}
