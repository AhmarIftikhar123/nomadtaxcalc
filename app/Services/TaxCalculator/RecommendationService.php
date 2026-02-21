<?php

namespace App\Services\TaxCalculator;

use App\Models\Country;

class RecommendationService
{
    /**
     * Generate smart recommendations based on calculation results
     */
    public function generate(array $residencyResults, array $taxBreakdown, float $totalTax): array
    {
        $recommendations = [];

        // Tax optimization opportunities
        $recommendations = array_merge($recommendations, $this->getTaxOptimization($residencyResults, $taxBreakdown));

        // Residency restructuring
        $recommendations = array_merge($recommendations, $this->getResidencyRestructuring($residencyResults));

        // Zero-tax opportunities
        $recommendations = array_merge($recommendations, $this->getZeroTaxOpportunities($totalTax));

        return $recommendations;
    }

    private function getTaxOptimization(array $residencyResults, array $taxBreakdown): array
    {
        $recommendations = [];

        // Find highest tax country
        if (!empty($taxBreakdown)) {
            $highestTax = collect($taxBreakdown)->sortByDesc('tax_due')->first();
            
            if ($highestTax && $highestTax['tax_due'] > 0) {
                $recommendations[] = [
                    'type' => 'tax_optimization',
                    'priority' => 'high',
                    'title' => 'Reduce time in high-tax countries',
                    'message' => "You paid the most tax in {$highestTax['country_name']} ({$highestTax['currency']} " . number_format($highestTax['tax_due']) . "). Consider reducing your stay below the {$highestTax['threshold']} day threshold.",
                ];
            }
        }

        return $recommendations;
    }

    private function getResidencyRestructuring(array $residencyResults): array
    {
        $recommendations = [];
        // dd($residencyResults);
        foreach ($residencyResults as $result) {
            if ($result['is_tax_resident'] && isset($result['threshold'])) {
                $daysDiff = $result['days_spent'] - $result['threshold'];
                
                if ($daysDiff >= 0 && $daysDiff <= 30) {
                    $recommendations[] = [
                        'type' => 'residency_optimization',
                        'priority' => 'medium',
                        'title' => 'Barely a tax resident of ' . $result['country_name'],
                        'message' => "You exceeded the threshold by only {$daysDiff} days. With minor travel adjustments, you could avoid tax residency next year.",
                    ];
                }
            }
        }

        return $recommendations;
    }

    private function getZeroTaxOpportunities(float $totalTax): array
    {
        $recommendations = [];

        // Suggest zero-tax countries
        $zeroTaxCountries = Country::where('flat_tax_rate', 0)
            ->where('is_active', true)
            ->where('has_digital_nomad_visa', true)
            ->take(3)
            ->get();

        if ($totalTax > 10000 && $zeroTaxCountries->isNotEmpty()) {
            $countryNames = $zeroTaxCountries->pluck('name')->join(', ');
            
            $recommendations[] = [
                'type' => 'zero_tax',
                'priority' => 'high',
                'title' => 'Consider zero-tax jurisdictions',
                'message' => "With your tax burden of $" . number_format($totalTax) . ", you could save significantly by spending more time in zero-tax countries like {$countryNames}.",
            ];
        }

        return $recommendations;
    }
}
