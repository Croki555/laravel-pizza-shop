<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StrictAddressValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $apiKey = config('services.yandex.geocoder_key');
        $response = Http::get('https://geocode-maps.yandex.ru/v1/', [
            'apikey' => $apiKey,
            'geocode' => $value,
            'lang' => 'ru-RU',
            'format' => 'json',
        ]);

//        Log::info('Проверка адреса: ' . $value);
//        Log::info('API Key: ' . config('services.yandex.geocoder_key'));
//        Log::info('Ответ от Яндекс.Геокодера: ', $response->json());

        $data = $response->json();

        if (empty($data['response']['GeoObjectCollection']['featureMember'])) {
            $fail('Адрес не найден в Яндекс.Картах.');
            return;
        }

        $firstResult = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'];
        if ($firstResult['metaDataProperty']['GeocoderMetaData']['precision'] !== 'exact') {
            $fail('Уточните адрес: ' . $firstResult['metaDataProperty']['GeocoderMetaData']['text']);
        }
    }

}
