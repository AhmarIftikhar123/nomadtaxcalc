<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Country;
use App\Models\State;
use App\Models\UserCalculation;
use App\Models\UserCalculationCountry;
use App\Services\TaxCalculator\TaxCalculatorService;
use App\Models\TaxType;

$service = app(TaxCalculatorService::class);

// Scenario: US Citizen, living in UK for 300 days. Makes $100k
$us = Country::where('iso_code', 'US')->first();
$uk = Country::where('iso_code', 'GB')->first();
$california = State::where('code', 'CA')->first();

echo "Running Scenario 1: US Citizen (CA) living in UK for 300 days. Income: $100,000 USD\n";

// Create calculation mock
$calc = UserCalculation::create([
    'session_uuid' => 'test-session-1-' . rand(),
    'country_id' => $us->id,
    'domicile_state_id' => $california->id,
    'gross_income' => 100000,
    'currency' => 'USD',
    'tax_year' => 2026,
    'citizenship_country_code' => 'US',
    'ip_address' => '127.0.0.1',
    'device_type' => 'desktop',
    'step_reached' => 2,
]);

$incomeTaxId = TaxType::where('key', 'income_tax')->first()->id;

$service->saveStep2Data($calc, [
    [
        'country_id' => $uk->id,
        'days' => 300,
        'selected_tax_types' => [
            [
                'tax_type_id' => $incomeTaxId,
                'is_custom' => false
            ]
        ]
    ],
    [
        'country_id' => $us->id,
        'days' => 65,
        'selected_tax_types' => [
            [
                'tax_type_id' => $incomeTaxId,
                'is_custom' => false
            ]
        ]
    ]
]);

$result = $service->calculateTaxes($calc);

echo "\n--- SCENARIO 1 RESULT ---\n";
echo "Total Tax: $" . $result['total_tax'] . "\n";
echo "Effective Rate: " . $result['effective_tax_rate'] . "%\n";
echo "Treaties Applied: " . json_encode($result['treaties_applied'], JSON_PRETTY_PRINT) . "\n";
if (!empty($result['feie_result'])) {
    echo "FEIE Applied: " . ($result['feie_result']['eligible'] ? 'Yes' : 'No') . " - Excluded: $" . $result['feie_result']['excluded_income'] . "\n";
}
echo "Breakdown:\n";
foreach ($result['breakdown_by_country'] as $cb) {
    echo "  Country: " . $cb['country_name'] . "\n";
    echo "  Tax Due: $" . $cb['tax_due'] . "\n";
    foreach ($cb['tax_type_breakdown'] as $ttb) {
        echo "    - " . $ttb['name'] . ": $" . $ttb['amount'] . "\n";
    }
}


echo "\n\nRunning Scenario 2: US Citizen residing in US (NY) all year. Income: $150,000 USD\n";
$ny = State::where('code', 'NY')->first();

$calc2 = UserCalculation::create([
    'session_uuid' => 'test-session-2-' . rand(),
    'country_id' => $us->id,
    'domicile_state_id' => $ny->id, // NY
    'gross_income' => 150000,
    'currency' => 'USD',
    'tax_year' => 2026,
    'citizenship_country_code' => 'US',
    'ip_address' => '127.0.0.1',
    'device_type' => 'desktop',
    'step_reached' => 2,
]);

$service->saveStep2Data($calc2, [
    [
        'country_id' => $us->id,
        'state_id' => $ny->id, // NY
        'days' => 365,
        'selected_tax_types' => [
            [
                'tax_type_id' => $incomeTaxId,
                'is_custom' => false
            ]
        ]
    ]
]);

$result2 = $service->calculateTaxes($calc2);

echo "\n--- SCENARIO 2 RESULT ---\n";
echo "Total Tax: $" . $result2['total_tax'] . "\n";
echo "FEIE Eligible: " . ($result2['feie_result']['eligible'] ? 'Yes' : 'No') . "\n";
echo "Breakdown:\n";
foreach ($result2['breakdown_by_country'] as $cb) {
    echo "  Country: " . $cb['country_name'] . " (Tax: $" . $cb['tax_due'] . ")\n";
    foreach ($cb['tax_type_breakdown'] as $ttb) {
         echo "    - " . $ttb['name'] . ": $" . $ttb['amount'] . "\n";
    }
}

echo "\nTests Complete.\n";
