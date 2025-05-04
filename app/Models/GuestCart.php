<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="GuestCart",
 *     required={"product_id", "quantity"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1
 *     ),
 *    @OA\Property(
 *          property="guest_token",
 *          type="string",
 *          description="Токен не зарегистрированно пользователя хранитя в куках, создается если его нет и используется для последующих запросах",
 *          example="0weE7Iw18Vuwx8Z6rfKUHqtFbFjKCqXUbGLxpHFt"
 *     ),
 *     @OA\Property(
 *         property="product_id",
 *         type="integer",
 *         description="ID продукта в корзине",
 *         example=5
 *     ),
 *     @OA\Property(
 *          property="product",
 *          ref="#/components/schemas/Product",
 *          description="Связанный продукт (если включено eager loading)"
 *      ),
 *     @OA\Property(
 *         property="quantity",
 *         type="integer",
 *         description="Количество товара",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         description="Дата создания записи",
 *         example="2023-01-01T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         description="Дата последнего обновления",
 *         example="2023-01-01T12:00:00Z"
 *     )
 * )
 */
class GuestCart extends Model
{
    use HasFactory;

    protected $table = 'guest_carts';
    protected $fillable = [
        'guest_token',
        'product_id',
        'quantity',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
