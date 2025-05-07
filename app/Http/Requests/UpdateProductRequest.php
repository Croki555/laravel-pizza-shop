<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Rules\CategoryExistsValidation;
use App\Rules\StrictIntegerValidation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:1'],
            'description' => ['nullable', 'string'],
            'category_id' => [
                'nullable',
                new CategoryExistsValidation()
            ],
        ];
    }
}
