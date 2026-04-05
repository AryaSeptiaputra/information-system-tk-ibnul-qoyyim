<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeItem extends Model
{
    use HasFactory;

    protected $table = 'fee_items';
    protected $primaryKey = 'id_fee_item';
    public $timestamps = true;

    protected $fillable = [
        'id_payment_type',
        'code',
        'name',
        'description',
        'default_amount',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'default_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class, 'id_payment_type', 'id_payment_type');
    }

    public function paymentItems(): HasMany
    {
        return $this->hasMany(StudentPaymentItem::class, 'id_fee_item', 'id_fee_item');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now())
                  ->where(function ($q2) {
                      $q2->whereNull('end_date')
                         ->orWhere('end_date', '>=', now());
                  });
            });
    }

    public function scopeByPaymentType($query, $paymentTypeId)
    {
        return $query->where('id_payment_type', $paymentTypeId);
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->is_active && 
               (!$this->start_date || $this->start_date <= now()) &&
               (!$this->end_date || $this->end_date >= now());
    }

    public function getAmount(): float
    {
        return (float) $this->default_amount;
    }
}
