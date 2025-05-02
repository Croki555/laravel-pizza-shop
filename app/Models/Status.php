<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Status",
 *     required={"name"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Название статуса",
 *         example="В обработке"
 *     ),
 * )
 */
class Status extends Model
{
    use HasFactory;

    protected $table = 'statuses';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
