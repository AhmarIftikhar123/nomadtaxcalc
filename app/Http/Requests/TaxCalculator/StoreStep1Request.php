<?php

namespace App\Http\Requests\TaxCalculator;

use Illuminate\Foundation\Http\FormRequest;

class StoreStep1Request extends FormRequest
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
            'annual_income' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'citizenship_country_id' => 'required|exists:countries,id',
            'tax_year' => 'required|integer|min:2020|max:2099',
            'domicile_state_id' => 'nullable|exists:states,id',
        ];
    }
}
