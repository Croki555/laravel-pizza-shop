<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Register/Login",
 *     description="API Endpoint for Register/Login"
 * )
 */
class RegisterController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Создание нового пользователя",
     *     tags={"Register/Login"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Данные пользователя",
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Иван Иванов",
     *                 description="ФИО пользователя"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 example="ivan@example.com",
     *                 description="Email пользователя"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 example="password123",
     *                 description="Пароль (минимум 8 символов, только английские буквы и цифры)"
     *             ),
     *             @OA\Property(
     *                 property="password_confirmation",
     *                 type="string",
     *                 format="password",
     *                 example="password123",
     *                 description="Подтверждение пароля"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Пользователь успешно создан",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", ref="#/components/schemas/UserResource"),
     *              @OA\Property(property="token", type="string", example="1|abcdef123456")
     *          )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error: Unprocessable Content",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Email обязателен для заполнения"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="Email обязателен для заполнения"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function __invoke(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        return response()->json([
            'user' => new UserResource($user),
            'token' => $user->createToken('auth-token')->plainTextToken
        ], 201);
    }
}
