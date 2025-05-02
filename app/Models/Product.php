<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Product",
 *     required={"name", "description", "price", "category_id"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *          property="category_id",
 *          type="integer",
 *          format="int64",
 *          description="ID категории продукта",
 *          example=1
 *      ),
 *     @OA\Property(
 *          property="category",
 *          ref="#/components/schemas/Category",
 *          description="Категория продукта (пицца, напиток)",
 *          example=2
 *      ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Название продукта",
 *         example="Пепперони"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Описание продукта",
 *         example="Пицца с колбасками пепперони и сыром"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="Цена продукта",
 *         example=12.99
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Дата создания",
 *         example="2023-01-01T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Дата обновления",
 *         example="2023-01-01T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="deleted_at",
 *         type="string",
 *         format="date-time",
 *         description="Дата удаления (мягкое удаление)",
 *         nullable=true,
 *         example=null
 *     )
 * )
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
    ];
}
