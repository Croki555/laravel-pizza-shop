<?php

namespace App\Http\Requests;

use App\Rules\StrictAddressValidation;
use App\Rules\StrictDeliveryTimeValidation;
use App\Rules\ValidateYandexAddress;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'regex:/^(\+7|8)[\s\-]?\(?\d{3}\)?[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}$/'
            ],
            'email' => ['required', 'email'],
            'delivery_address' => ['required', 'string', new StrictAddressValidation()],
            'delivery_time' => [
                'required',
                'date_format:Y-m-d H:i:s',
                new StrictDeliveryTimeValidation(),
            ]
        ];
    }

    public function messages()
    {
        return [
            'phone.regex' => 'Номер телефона должен быть в формате: +7 (XXX) XXX-XX-XX или 8 XXX XXX-XX-XX',
        ];
    }

}
