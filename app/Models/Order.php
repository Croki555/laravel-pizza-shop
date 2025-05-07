<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'phone',
        'email',
        'delivery_address',
        'delivery_time',
        'deleted_at',
        'status_id',
    ];

    protected $casts = [
        'delivery_time' => 'datetime:Y-m-d H:i:s'
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function itemsWithProducts(): HasMany
    {
        return $this->hasMany(OrderItem::class)->with('product');
    }
}
