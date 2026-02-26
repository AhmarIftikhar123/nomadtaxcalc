"use client";

import React from "react";
import { Link } from "@inertiajs/react";
import { TriangleAlert, Clock, Share2, ArrowRight } from "lucide-react";
import TaxCalculatorLayout from "@/Layouts/TaxCalculatorLayout";
import ResultMetricsCards from "@/Components/TaxCalculator/ResultMetricsCards";
import TaxCalculationFlow from "@/Components/TaxCalculator/TaxCalculationFlow";
import TaxLiabilityComparison from "@/Components/TaxCalculator/TaxLiabilityComparison";
import DetailedTaxBreakdown from "@/Components/TaxCalculator/DetailedTaxBreakdown";
import TreatiesApplied from "@/Components/TaxCalculator/TreatiesApplied";
import FEIEStatus from "@/Components/TaxCalculator/FEIEStatus";
import DisclaimerBanner from "@/Components/ui/DisclaimerBanner";

export default function SharedCalculationShow({
    expired,
    expiredAt,
    result,
    citizenshipCode,
    shareExpiresAt,
}) {
    // ─── Expired / Not Found State ──────────────────────────────────────────
    if (expired || !result) {
        return (
            <TaxCalculatorLayout title="Shared Tax Results">
                <div className="max-w-2xl mx-auto px-6 py-24 text-center">
                    {/* Expired Icon */}
                    <div className="w-20 h-20 rounded-full bg-amber-50 border-2 border-amber-200 flex items-center justify-center mx-auto mb-8">
                        <Clock className="w-10 h-10 text-amber-500" />
                    </div>

                    <h1 className="text-4xl font-bold text-primary mb-4">
                        {expiredAt ? "Link Expired" : "Link Not Found"}
                    </h1>
                    <p className="text-lg text-gray mb-10 max-w-md mx-auto">
                        {expiredAt
                            ? `This shared tax results link expired on ${expiredAt}. The owner can generate a new 30-day link from their results page.`
                            : "This link doesn't exist or may have already been deactivated."}
                    </p>

                    <Link
                        href={route("tax-calculator.index")}
                        className="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-dark text-light font-bold rounded-lg transition-all"
                    >
                        Calculate Your Own Taxes
                        <ArrowRight className="w-5 h-5" />
                    </Link>
                </div>
            </TaxCalculatorLayout>
        );
    }

    const titleYear = result.tax_year || new Date().getFullYear();

    return (
        <TaxCalculatorLayout title={`Shared Tax Results — ${titleYear}`}>
            <div className="max-w-6xl mx-auto px-6 md:px-8 py-12">
                {/* ─── Page Header ─────────────────────────────────────────── */}
                <div className="mb-10 flex items-start justify-between flex-wrap gap-4">
                    <div>
                        <div className="flex items-center gap-3 mb-2">
                            <div className="p-2 bg-primary/10 rounded-lg">
                                <Share2 className="w-5 h-5 text-primary" />
                            </div>
                            <span className="text-sm font-semibold text-gray uppercase tracking-wider">
                                Shared Results
                            </span>
                        </div>
                        <h1 className="text-4xl md:text-5xl font-bold text-primary">
                            Tax Calculation Results — {titleYear}
                        </h1>
                        <p className="text-lg text-gray mt-2">
                            Read-only view shared by the owner.
                        </p>
                    </div>

                    {/* Expiry chip */}
                    {shareExpiresAt && (
                        <div className="flex items-center gap-2 px-4 py-2 bg-amber-50 border border-amber-300 rounded-lg text-amber-700 text-sm font-medium self-start mt-1">
                            <Clock className="w-4 h-4 flex-shrink-0" />
                            Link expires {shareExpiresAt}
                        </div>
                    )}
                </div>

                {/* ─── Disclaimer ──────────────────────────────────────────── */}
                <DisclaimerBanner />

                {/* ─── Metrics ─────────────────────────────────────────────── */}
                <div className="mt-8 space-y-6">
                    <ResultMetricsCards result={result} />
                </div>

                {/* ─── Calculation Flow ─────────────────────────────────────── */}
                <div className="mt-6">
                    <TaxCalculationFlow result={result} />
                </div>

                {/* ─── Breakdown ───────────────────────────────────────────── */}
                {result.comparison_data?.length > 0 && (
                    <div className="mt-6">
                        <TaxLiabilityComparison
                            comparisonData={result.comparison_data}
                            currency={result.currency}
                        />
                    </div>
                )}

                {result.breakdown_by_country?.length > 0 && (
                    <div className="mt-6">
                        <DetailedTaxBreakdown
                            breakdownData={result.breakdown_by_country}
                            currency={result.currency}
                            taxYear={result.tax_year}
                        />
                    </div>
                )}

                {/* ─── Treaties ────────────────────────────────────────────── */}
                {result.treaties_applied?.length > 0 && (
                    <div className="mt-6">
                        <TreatiesApplied
                            treatiesApplied={result.treaties_applied}
                            currency={result.currency}
                        />
                    </div>
                )}

                {/* ─── FEIE ─────────────────────────────────────────────────── */}
                {result.feie_result && (
                    <div className="mt-6">
                        <FEIEStatus
                            feieResult={result.feie_result}
                            citizenshipCountryCode={citizenshipCode}
                            currency={result.currency}
                            taxYear={result.tax_year}
                        />
                    </div>
                )}

                {/* ─── Read-only Footer CTA ─────────────────────────────────── */}
                <div className="mt-16 mb-8 p-8 bg-white rounded-xl border border-border-gray text-center shadow-sm">
                    <p className="text-gray text-base mb-6 max-w-lg mx-auto">
                        Want to calculate your own tax obligations across
                        multiple countries? Use our free tax calculator.
                    </p>
                    <Link
                        href={route("tax-calculator.index")}
                        className="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-dark text-light font-bold rounded-lg transition-all"
                    >
                        Calculate Your Own Taxes
                        <ArrowRight className="w-5 h-5" />
                    </Link>
                </div>

                {/* ─── Legal disclaimer row ─────────────────────────────────── */}
                <p className="text-center text-xs text-gray/70 pb-8">
                    This is a read-only shared view. Results are estimates only
                    and do not constitute tax advice.
                </p>
            </div>
        </TaxCalculatorLayout>
    );
}
