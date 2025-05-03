<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireJsonAcceptHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->is('api/*')) {
            return $next($request);
        }
        if (!$request->wantsJson()) {
            return response()->json([
                'error' => 'Отсутствует или недействителен заголовок Accept',
                'message' => 'Запрос должен включать заголовок "Accept: application/json"',
            ], 406);
        }

        return $next($request);
    }
}
