<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'before_qty',
        'after_qty',
        'reference_type',
        'reference_id',
        'notes',
        'user_id',
        'transaction_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'before_qty' => 'integer',
        'after_qty' => 'integer',
        'transaction_date' => 'datetime',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Polymorphic reference
    public function reference()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeStockIn($query)
    {
        return $query->where('type', 'in');
    }

    public function scopeStockOut($query)
    {
        return $query->where('type', 'out');
    }

    public function scopeAdjustment($query)
    {
        return $query->where('type', 'adjustment');
    }
}
