<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class DeleteAccountRequest extends FormRequest
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
            'password' => 'required|string|min:8',
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = auth()->user(); // Usuário autenticado
            $password = $this->input('password');

            if (!Hash::check($password, $user->password)) {
                $validator->errors()->add('password', 'Password incorrect.');
            }
        });
    }
}
