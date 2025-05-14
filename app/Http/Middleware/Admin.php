<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{

    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = auth()->user();

        if (!$user->is_admin) {
            return response()->json([
                'message' => 'Доступ запрещён: требуются права администратора'
            ], 403);
        }

        return $next($request);
    }
}
