<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\User;
use App\Rules\CategoryExistsValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $user = auth()->user();
        return $user->is_admin;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:1'],
            'description' => ['required', 'string'],
            'category_id' => [
                'prohibits:category',
                'required_without:category',
                new CategoryExistsValidation(),
            ],
            'category' => [
                'prohibits:category_id',
                'required_without:category_id',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $normalized = mb_strtolower(trim($value));

                    $existingCategory = Category::query()
                        ->whereRaw('LOWER(TRIM(name)) LIKE ?', [str_replace('%', '\%', $normalized)])
                        ->first();

                    if ($existingCategory) {
                        $fail(sprintf(
                            'Категория "%s" уже существует',
                            $existingCategory->name,
                        ));
                    }
                },
            ],
        ];
    }
}
