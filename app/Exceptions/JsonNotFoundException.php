<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class JsonNotFoundException extends Exception
{
    public function __construct($message = null)
    {
        parent::__construct($message ?? 'Ресурс не найден');
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->message,
            'errors' => [
                'id' => ['Запрашиваемый ресурс не существует']
            ]
        ], 404);
    }
}
