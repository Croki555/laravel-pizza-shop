<?php

declare(strict_types=1);

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrictDeliveryTimeValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $deliveryTime = Carbon::parse($value);

            $hour = $deliveryTime->hour;
            if ($hour < 10 || $hour >= 22) {
                $fail('Доставка возможна только с 10:00 до 22:00.');
            }

            // Проверка на выходные
            if ($deliveryTime->isWeekend()) {
                $fail('Доставка недоступна в выходные дни (суббота и воскресенье).');
            }

        } catch (\Exception $e) {
            $fail('Некорректный формат времени доставки.');
        }
    }
}
