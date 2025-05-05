<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

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
