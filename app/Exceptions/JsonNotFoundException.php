<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class JsonNotFoundException extends Exception
{
    public function __construct(string $message = 'Ресурс не найден')
    {
        parent::__construct($message);
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
