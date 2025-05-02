<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Cart",
 *     required={"user_id", "product_id", "quantity"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID пользователя, которому принадлежит корзина",
 *         example=1
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
class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];
}
