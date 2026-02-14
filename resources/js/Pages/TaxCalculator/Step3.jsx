"use client";

import React from "react";
import { Link } from "@inertiajs/react";
import { RotateCcw, Download } from "lucide-react";
import Footer from "@/Components/Footer";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import TopBar from "@/Components/TopBar";
import ResultMetricsCards from "@/Components/TaxCalculator/ResultMetricsCards";
import ResidencyRiskAlert from "@/Components/TaxCalculator/ResidencyRiskAlert";
import TaxLiabilityComparison from "@/Components/TaxCalculator/TaxLiabilityComparison";
import SmartRecommendations from "@/Components/TaxCalculator/SmartRecommendations";
import DetailedTaxBreakdown from "@/Components/TaxCalculator/DetailedTaxBreakdown";

export default function Step3({ auth, result }) {
    const isAuthenticated = auth?.user;

    // Destructure all data directly from backend result
    const {
        annual_income,
        currency,
        total_tax,
        net_income,
        effective_tax_rate,
        breakdown_by_country = [],
        recommendations = [],
        residency_warnings = [],
        residency_data = [],
        comparison_data = [],
        treaties_applied = [],
        feie_result = null,
    } = result;

    // Map to component-friendly names
    const resultData = result;
    const breakdownData = breakdown_by_country;
    const comparisonData = comparison_data;

    // Pass full residency data array to ResidencyRiskAlert
    // (component now handles filtering by has_income_tax and threshold)
    const residencyAlertData = residency_data;
    const handleRecalculate = () => {
        window.location.href = route("tax-calculator.index");
    };

    // If user is authenticated, show dashboard layout
    if (isAuthenticated) {
        return (
            <AuthenticatedLayout
                user={auth.user}
                title="Tax Calculation Results"
                onRecalculate={handleRecalculate}
            >
                {/* Page Content */}
                <div className="max-w-6xl mx-auto px-6 md:px-8 py-12">
                    {/* Progress Section */}
                    <div className="mb-12 flex justify-between items-end">
                        <div>
                            <h1 className="text-4xl md:text-5xl font-bold text-primary mb-2">
                                Tax Calculation Results
                            </h1>
                            <p className="text-lg text-gray">
                                Step 3 of 3: Your personalized tax analysis
                            </p>
                        </div>
                        <div className="text-right">
                            <p className="text-sm font-bold text-primary mb-2">
                                100% Completed
                            </p>
                            <div className="w-32 h-1.5 bg-border-gray rounded-full overflow-hidden">
                                <div className="w-full h-full bg-primary" />
                            </div>
                        </div>
                    </div>

                    {/* Metrics Cards */}
                    <ResultMetricsCards result={result} />

                    {/* Residency Risk Alert */}
                    <ResidencyRiskAlert residencyData={residencyAlertData} />

                    {/* Main Content Grid */}
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                        {/* Left Column - Chart and Breakdown */}
                        <div className="lg:col-span-2">
                            {/* Tax Liability Comparison */}
                            <TaxLiabilityComparison
                                comparisonData={comparisonData}
                                currency={result.currency}
                            />

                            {/* Detailed Tax Breakdown */}
                            <DetailedTaxBreakdown
                                breakdownData={breakdownData}
                                currency={result.currency}
                            />
                        </div>

                        {/* Right Column - Recommendations */}
                        <div>
                            <SmartRecommendations
                                recommendations={recommendations}
                                currency={result.currency}
                            />
                        </div>
                    </div>

                    {/* Action Buttons */}
                    <div className="flex gap-4 justify-center flex-wrap mb-12">
                        <Link
                            href={route("tax-calculator.index")}
                            className="px-8 py-4 border-2 border-primary text-primary font-bold rounded-lg hover:bg-primary hover:text-light transition-all flex items-center gap-2"
                        >
                            <RotateCcw className="w-5 h-5" />
                            Start Over
                        </Link>
                        <button
                            onClick={() => window.print()}
                            className="px-8 py-4 bg-primary hover:bg-dark text-light font-bold rounded-lg transition-all flex items-center gap-2"
                        >
                            <Download className="w-5 h-5" />
                            Print Results
                        </button>
                    </div>
                </div>
            </AuthenticatedLayout>
        );
    }

    // If user is not authenticated, show public layout
    return (
        <>
            {/* Top Bar */}
            <TopBar
                title="Tax Calculation Results"
                onRecalculate={handleRecalculate}
            />
            <main className="min-h-screen bg-light">
                <div className="max-w-6xl mx-auto px-6 md:px-8 py-12">
                    {/* Progress Section */}
                    <div className="mb-12 flex justify-between items-end">
                        <div>
                            <h1 className="text-4xl md:text-5xl font-bold text-primary mb-2">
                                Tax Calculation Results
                            </h1>
                            <p className="text-lg text-gray">
                                Your personalized tax analysis
                            </p>
                        </div>
                        <div className="text-right">
                            <p className="text-sm font-bold text-primary mb-2">
                                100% Completed
                            </p>
                            <div className="w-32 h-1.5 bg-border-gray rounded-full overflow-hidden">
                                <div className="w-full h-full bg-primary" />
                            </div>
                        </div>
                    </div>

                    {/* Metrics Cards */}
                    <ResultMetricsCards result={result} />

                    {/* Residency Risk Alert */}
                    <ResidencyRiskAlert residencyData={residencyAlertData} />

                    {/* Main Content Grid */}
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                        {/* Left Column - Chart and Breakdown */}
                        <div className="lg:col-span-2">
                            {/* Tax Liability Comparison */}
                            <TaxLiabilityComparison
                                comparisonData={comparisonData}
                                currency={result.currency}
                            />

                            {/* Detailed Tax Breakdown */}
                            <DetailedTaxBreakdown
                                breakdownData={breakdownData}
                                currency={result.currency}
                            />
                        </div>

                        {/* Right Column - Recommendations */}
                        <div>
                            <SmartRecommendations
                                recommendations={recommendations}
                                currency={result.currency}
                            />
                        </div>
                    </div>

                    {/* Action Buttons */}
                    <div className="flex gap-4 justify-center flex-wrap mb-12">
                        <Link
                            href={route("tax-calculator.index")}
                            className="px-8 py-4 border-2 border-primary text-primary font-bold rounded-lg hover:bg-primary hover:text-light transition-all flex items-center gap-2"
                        >
                            <RotateCcw className="w-5 h-5" />
                            Start Over
                        </Link>
                        <button
                            onClick={() => window.print()}
                            className="px-8 py-4 bg-primary hover:bg-dark text-light font-bold rounded-lg transition-all flex items-center gap-2"
                        >
                            <Download className="w-5 h-5" />
                            Print Results
                        </button>
                    </div>
                </div>
            </main>
            <Footer />
        </>
    );
}
