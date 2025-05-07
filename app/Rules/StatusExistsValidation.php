<?php

namespace App\Rules;

use App\Models\Status;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StatusExistsValidation implements ValidationRule
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

        $status = Status::find($value);

        if (!$status) {
            $availableCategories = Status::all(['id', 'name'])
                ->mapWithKeys(fn ($item) => [$item->id => $item->name])
                ->toArray();

            $formattedCategories = collect($availableCategories)
                ->map(fn ($name, $id) => "#$id - $name")
                ->implode(', ');

            $fail("Статус с ID $value не найден. Доступные статусы: $formattedCategories");
        }
    }
}
