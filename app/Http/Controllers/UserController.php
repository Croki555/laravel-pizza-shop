<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;


/**
 * @OA\Tag(
 *     name="User",
 *     description="Операции с текущим аутентифицированным пользователем"
 * )
 */
class UserController extends Controller
{
    /**
     * Получить информацию о текущем пользователе
     *
     * @OA\Get(
     *     path="/api/user",
     *     summary="Получить данные аутентифицированного пользователя",
     *     description="Возвращает информацию о текущем пользователе, аутентифицированном через Sanctum",
     *     operationId="getCurrentUser",
     *     tags={"User"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Успешный запрос",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Не авторизован",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function __invoke(Request $request)
    {
        return new UserResource($request->user());
    }
}
