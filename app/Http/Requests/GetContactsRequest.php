<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetContactsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'order_by' => 'nullable|string|in:name,cpf',
            'order_type' => 'nullable|string|in:asc,desc',
        ];
    }
}
