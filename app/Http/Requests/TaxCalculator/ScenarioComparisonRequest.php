<?php

namespace App\Http\Requests\TaxCalculator;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class ScenarioComparisonRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'step1'      => 'required|array',
            'scenarioA'  => 'required|array',
            'scenarioB'  => 'required|array',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $step1 = $this->input('step1', []);
            $year = $step1['tax_year'] ?? date('Y');
            
            try {
                $isLeapYear = Carbon::createFromDate($year)->isLeapYear();
            } catch (\Exception $e) {
                $isLeapYear = false;
            }
            
            $maxDays = $isLeapYear ? 366 : 365;

            $scenarios = ['scenarioA' => 'Scenario A', 'scenarioB' => 'Scenario B'];
            foreach ($scenarios as $key => $label) {
                $items = $this->input($key, []);
                if (is_array($items)) {
                    $totalDays = collect($items)->sum(function ($item) {
                        return (int) ($item['days_spent'] ?? 0);
                    });

                    if ($totalDays > $maxDays) {
                        $validator->errors()->add($key, "Total days spent in {$label} cannot exceed {$maxDays} days for the tax year {$year}.");
                    }
                }
            }
        });
    }
}
