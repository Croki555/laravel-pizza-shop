<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Order",
 *     required={"user_id", "phone", "email","delivery_address"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         format="int64",
 *         description="ID пользователя",
 *         example=4
 *     ),
 *     @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/User",
 *         description="Информация о пользователе",
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         example="+79214074603",
 *         description="Номер телефона"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         example="denis.fad@example.com",
 *         description="Электронная почта"
 *     ),
 *     @OA\Property(
 *         property="delivery_address",
 *         type="string",
 *         example="Улица Шевченко дом 3 корпус 2, кв 92 этаж 9",
 *         description="Адрес доставки"
 *     ),
 *     @OA\Property(
 *          property="status_id",
 *          type="integer",
 *          format="int64",
 *          description="ID статуса заказа",
 *          example=4
 *      ),
 *      @OA\Property(
 *          property="status",
 *          ref="#/components/schemas/Status",
 *          description="Информация о статусе заказа",
 *      ),
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
class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'phone',
        'email',
        'delivery_address',
        'status_id',
    ];
}
