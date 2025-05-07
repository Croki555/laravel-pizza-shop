<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
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
                'integer',
                function ($attribute, $value, $fail) {
                    $category = Category::find($value);

                    if (!$category) {
                        $availableCategories = Category::all(['id', 'name'])
                            ->mapWithKeys(fn ($item) => [$item->id => $item->name])
                            ->toArray();

                        $formattedCategories = collect($availableCategories)
                            ->map(fn ($name, $id) => "#$id - $name")
                            ->implode(', ');

                        $fail("Категория с ID $value не найдена. Доступные категории: $formattedCategories");
                    }
                }
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
                }
            ]
        ];
    }
}
