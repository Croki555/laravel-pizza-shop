<?php

namespace App\Rules;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CategoryExistsValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if (!is_int($value)) {
            $fail('Поле :attribute должно быть строго целым числом (не строкой)');
            return;
        }

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
}
