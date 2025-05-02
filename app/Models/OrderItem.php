<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="OrderItem",
 *     required={"order_id", "product_id", "quantity"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         example=1
 *     ),
 *     @OA\Property(
 *          property="order_id",
 *          type="integer",
 *          format="int64",
 *          description="ID заказа",
 *          example=24
 *     ),
 *     @OA\Property(
 *          property="order",
 *          ref="#/components/schemas/Order",
 *          description="Информация о заказе",
 *     ),
 *     @OA\Property(
 *         property="product_id",
 *           type="integer",
 *           format="int64",
 *           description="ID продукта",
 *           example=5
 *      ),
 *      @OA\Property(
 *           property="product",
 *           ref="#/components/schemas/Product",
 *           description="Информация о продукте",
 *      ),
 *     @OA\Property(
 *         property="quantity",
 *         type="integer",
 *         format="int64",
 *         example=9,
 *         description="Колличество данного продукта"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2023-01-01T12:00:00Z",
 *         description="Дата создания"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2023-01-01T12:00:00Z",
 *         description="Дата обновления"
 *     )
 * )
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity'
    ];
}
