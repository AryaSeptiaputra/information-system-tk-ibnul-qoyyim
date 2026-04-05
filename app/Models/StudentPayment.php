<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentPayment extends Model
{
    use HasFactory;

    protected $table = 'student_payments';
    protected $primaryKey = 'id_student_payment';
    public $timestamps = true;

    protected $fillable = [
        'id_student',
        'id_payment_type',
        'payment_period',
        'total_amount',
        'discount_amount',
        'final_amount',
        'payment_method',
        'status',
        'is_late',
        'unique_code',
        'proof_file',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'is_late' => 'boolean',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_student', 'id_student');
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class, 'id_payment_type', 'id_payment_type');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StudentPaymentItem::class, 'id_student_payment', 'id_student_payment');
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, $paymentTypeId)
    {
        return $query->where('id_payment_type', $paymentTypeId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('id_student', $studentId);
    }

    public function scopeLate($query)
    {
        return $query->where('is_late', true);
    }

    // Helper Methods
    public function isLate(): bool
    {
        return $this->is_late;
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid' && $this->paid_at !== null;
    }

    public function markAsPaid(string $method = null, string $proofFile = null): void
    {
        $this->status = 'paid';
        $this->payment_method = $method ?? $this->payment_method;
        $this->proof_file = $proofFile ?? $this->proof_file;
        $this->paid_at = now();
        $this->save();

        // Update student status to aktif
        if ($this->student && $this->paymentType->code === 'REGISTRATION') {
            $this->student->markAsAktif();
            $this->student->registration->markAsActive();
        }
    }

    public function markAsFailed(): void
    {
        $this->status = 'failed';
        $this->save();
    }

    public function getTotalAmount(): float
    {
        return (float) $this->final_amount;
    }

    public function getDiscountedAmount(): float
    {
        return (float) ($this->total_amount - $this->discount_amount);
    }
}
