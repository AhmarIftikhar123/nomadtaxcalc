<?php

namespace App\Http\Requests\TaxCalculator;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreStep2Request extends FormRequest
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
            'residency_periods' => 'required|array|min:1',
            'residency_periods.*.country_id' => 'required|exists:countries,id|distinct',
            'residency_periods.*.state_id' => 'nullable|exists:states,id',
            'residency_periods.*.days' => 'required|integer|min:1|max:365',
            'residency_periods.*.selected_tax_types' => 'nullable|array',
            'residency_periods.*.selected_tax_types.*.is_custom' => 'nullable|boolean',
            'residency_periods.*.selected_tax_types.*.tax_type_id' => 'nullable',
            'residency_periods.*.selected_tax_types.*.custom_name' => 'nullable|string|max:100',
            'residency_periods.*.selected_tax_types.*.amount_type' => 'nullable|in:percentage,flat',
            'residency_periods.*.selected_tax_types.*.amount' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $totalDays = collect($this->residency_periods)->sum('days');
            if ($totalDays !== 365) {
                $validator->errors()->add('residency_periods', "Total days must equal 365. Current total: {$totalDays}");
            }
        });
    }
}
