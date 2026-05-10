<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnEscrow extends Model
{
    use HasFactory;

    protected $table = 'returns';

    public const TYPE_JUAL_BELI = 'jual_beli';
    public const TYPE_SEWA = 'sewa';

    public const STATUS_PENDING = 'pending';
    public const STATUS_DISPUTE = 'dispute';
    public const STATUS_CHECKING = 'checking';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REJECTED = 'rejected';

    public const TYPES = [
        self::TYPE_JUAL_BELI,
        self::TYPE_SEWA,
    ];

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_DISPUTE,
        self::STATUS_CHECKING,
        self::STATUS_COMPLETED,
        self::STATUS_REJECTED,
    ];

    protected $fillable = [
        'order_id',
        'type',
        'status',
        'escrow_total',
        'expected_date',
        'actual_date',
        'late_fee',
        'damage_fee',
        'to_seller',
        'to_buyer',
        'proof_sent_image',
        'proof_returned_image',
        'owner_notes',
        'renter_notes',
        'dispute_chat_log',
        'total_fines',
        'deficit',
        'deposit_amount',
        'rental_fee_amount',
    ];

    protected $casts = [
        'order_id' => 'integer',
        'escrow_total' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'rental_fee_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'damage_fee' => 'decimal:2',
        'to_seller' => 'decimal:2',
        'to_buyer' => 'decimal:2',
        'total_fines' => 'decimal:2',
        'deficit' => 'decimal:2',
        'expected_date' => 'datetime',
        'actual_date' => 'datetime',
        'dispute_chat_log' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
