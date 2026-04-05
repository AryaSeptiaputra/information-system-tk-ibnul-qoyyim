<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPaymentItem extends Model
{
    use HasFactory;

    protected $table = 'student_payment_items';
    protected $primaryKey = 'id_student_payment_item';
    public $timestamps = true;

    protected $fillable = [
        'id_student_payment',
        'id_fee_item',
        'item_code',
        'item_name',
        'description',
        'quantity',
        'unit_price',
        'discount',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relationships
    public function payment(): BelongsTo
    {
        return $this->belongsTo(StudentPayment::class, 'id_student_payment', 'id_student_payment');
    }

    public function feeItem(): BelongsTo
    {
        return $this->belongsTo(FeeItem::class, 'id_fee_item', 'id_fee_item');
    }

    // Helper Methods
    public function calculateSubtotal(): float
    {
        $subtotal = ($this->quantity * (float)$this->unit_price) - (float)$this->discount;
        return max(0, $subtotal);
    }

    public function getSubtotal(): float
    {
        return (float) $this->subtotal;
    }

    public function getItemName(): string
    {
        return $this->item_name;
    }

    public function getQuantity(): int
    {
        return (int) $this->quantity;
    }

    public function getUnitPrice(): float
    {
        return (float) $this->unit_price;
    }
}
