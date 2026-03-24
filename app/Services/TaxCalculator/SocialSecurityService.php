<?php

namespace App\Services\TaxCalculator;

use App\Models\Country;
use App\Models\SocialSecurityRule;
use App\Models\TaxTreaty;
use App\Services\CurrencyService;

/**
 * Calculate social security contributions for a given country.
 *
 * Handles totalization agreement checks, per-fund rate lookups,
 * income caps, and cross-currency conversion of contributions.
 */
class SocialSecurityService
{
    public function __construct(
        protected CurrencyService $currencyService,
    ) {}

    /**
     * Check if a totalization agreement exempts the user from SS in a country.
     *
     * @param  int   $citizenshipCountryId  User's citizenship country ID.
     * @param  int   $residenceCountryId    Country being checked.
     * @param  int   $taxYear               Tax year for treaty lookup.
     * @return bool  True if totalization agreement exists (SS exempt).
     */
    public function checkTotalization(int $citizenshipCountryId, int $residenceCountryId, int $taxYear = 2026): bool
    {
        $treaty = TaxTreaty::active()
            ->between($citizenshipCountryId, $residenceCountryId)
            ->where('applicable_tax_year', $taxYear)
            ->where('treaty_type', 'totalization')
            ->first();

        return $treaty !== null;
    }

    /**
     * Calculate social security contributions for a country using real DB rules.
     *
     * @param  Country     $country
     * @param  float       $income        Income in user's currency
     * @param  int         $taxYear
     * @param  string|null $userCurrency  User's income currency for conversion
     * @param  string      $type          'employee' or 'employer'
     * @return array{total: float, breakdown: array}
     */
    public function calculateSocialSecurity(
        Country $country,
        float $income,
        int $taxYear = 2026,
        ?string $userCurrency = null,
        string $type = 'employee',
    ): array {
        $rules = SocialSecurityRule::forCountryYear($country->id, $taxYear)
            ->where('contribution_type', $type)
            ->get();

        if ($rules->isEmpty()) {
            return ['total' => 0, 'breakdown' => []];
        }

        $totalContribution = 0;
        $breakdown = [];

        foreach ($rules as $rule) {
            // Convert income to rule's currency if needed
            $ruleCurrency = $rule->currency_code ?? $country->currency_code;
            $needsConversion = $userCurrency && $ruleCurrency && $userCurrency !== $ruleCurrency;

            $incomeInRuleCurrency = $needsConversion
                ? $this->currencyService->convert($income, $userCurrency, $ruleCurrency)
                : $income;

            $contribution = $rule->calculateContribution($incomeInRuleCurrency);

            // Convert contribution back to user currency
            if ($needsConversion && $contribution > 0) {
                $contribution = $this->currencyService->convert($contribution, $ruleCurrency, $userCurrency);
            }

            if ($contribution > 0) {
                $totalContribution += $contribution;
                $breakdown[] = [
                    'fund_name'    => $rule->fund_name,
                    'rate'         => (float) $rule->rate,
                    'contribution' => round($contribution, 2),
                    'currency'     => $ruleCurrency,
                    'type'         => $type,
                ];
            }
        }

        return [
            'total'     => round($totalContribution, 2),
            'breakdown' => $breakdown,
        ];
    }
}
