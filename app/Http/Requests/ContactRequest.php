<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'cpf' => [
                'required',
                'string',
                'size:11',
                Rule::unique('contacts')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })->ignore($this->route('id')),
            ],
            'phone' => 'required|string|max:15',
            'number' => 'required|int',
            'address' => 'required|string|max:255',
            'cep' => 'required|string|size:8',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:2',
            'country' => 'required|string|max:100',
            'complement' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $cpf = $this->input('cpf');
            if ($cpf && !$this->isValidCpf($cpf)) {
                $validator->errors()->add('cpf', 'The CPF is invalid.');
            }
        });
    }

    private function isValidCpf($cpf)
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) !== 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        $cpfBase = substr($cpf, 0, 9);
        $calculatedDigit1 = $this->calculateDigit($cpfBase, 10);
        $calculatedDigit2 = $this->calculateDigit($cpfBase . $calculatedDigit1, 11);

        return $calculatedDigit1 == $cpf[9] && $calculatedDigit2 == $cpf[10];
    }

    private function calculateDigit($cpfBase, $weight)
    {
        $sum = 0;
        for ($i = 0; $i < strlen($cpfBase); $i++) {
            $sum += $cpfBase[$i] * $weight--;
        }

        $remainder = $sum % 11;
        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
