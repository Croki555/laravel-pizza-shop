<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints for Users"
 * )
 */

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Список пользователей",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return User::all();
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Создание нового пользователя",
     *     tags={"Users"},
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
     *             type="object",
     *             @OA\Property(
     *                 property="User",
     *                 ref="#/components/schemas/User"
     *             )
     *         )
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
    public function store(StoreUserRequest $request)
    {
        return [
          'User' => User::create($request->validated()),
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Получить пользователя по ID",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="user",
     *                 ref="#/components/schemas/User"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User not found"
     *             )
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

    public function show($id)
    {
        $user =  User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'Ресурс не найден',
                'errors' => [
                    'id' => ['Запрашиваемый ресурс не существует']
                ]
            ], 404);

        }

        return ['user' => $user];
    }



/**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {

    }
}
