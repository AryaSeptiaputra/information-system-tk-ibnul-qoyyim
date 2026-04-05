<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentType extends Model
{
    use HasFactory;

    protected $table = 'payment_types';
    protected $primaryKey = 'id_payment_type';
    public $timestamps = true;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function payments(): HasMany
    {
        return $this->hasMany(StudentPayment::class, 'id_payment_type', 'id_payment_type');
    }

    public function feeItems(): HasMany
    {
        return $this->hasMany(FeeItem::class, 'id_payment_type', 'id_payment_type');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods
    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
