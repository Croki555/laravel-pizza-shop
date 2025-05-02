<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShowUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'user' => ['required', 'integer', Rule::exists('users', 'id')]
        ];
    }

    public function messages(): array
    {
        return [
            'user.exists' => 'Пользователь с указанным ID не найден'
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user' => $this->route('user') ?? $this->route('id')
        ]);
    }

}
