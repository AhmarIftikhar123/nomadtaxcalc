"use client";

import React from "react";
import { Link } from "@inertiajs/react";
import { RotateCcw, Download } from "lucide-react";
import Footer from "@/Components/Footer";
import Sidebar from "@/Components/Sidebar";
import TopBar from "@/Components/TopBar";
import ResultMetricsCards from "@/Components/TaxCalculator/ResultMetricsCards";
import ResidencyRiskAlert from "@/Components/TaxCalculator/ResidencyRiskAlert";
import TaxLiabilityComparison from "@/Components/TaxCalculator/TaxLiabilityComparison";
import SmartRecommendations from "@/Components/TaxCalculator/SmartRecommendations";
import DetailedTaxBreakdown from "@/Components/TaxCalculator/DetailedTaxBreakdown";

export default function Step3({
    auth = {
        user: {
            id: 1,
            name: "NomadTax",
            email: "user@example.com",
            avatar_url:
                "https://api.dicebear.com/7.x/avataaars/svg?seed=NomadTax",
            plan: "Plan Basic",
        },
    },
    result = {
        annual_income: 132000,
        currency: "USD",
        country_of_citizenship: "USA",
        total_estimated_tax: 24500,
        effective_tax_rate: 18.5,
        net_income_after_tax: 108000,
        business_expenses: 0,
        taxable_income: 132000,
        income_tax: 18000,
        self_employment_tax: 6500,
        total_tax: 24500,
        trend_indicator: "+2.4%",
        trend_type: "increase",
        trend_description: "vs last projection",
        income_trend: "+11% change",
        income_trend_type: "positive",
        net_income_trend: "-1.5% due to rate hike",
        net_income_trend_type: "negative",
    },
    comparisonData = [
        {
            country: "USA",
            code: "US",
            liability: 8500,
            color: "#22262a",
        },
        {
            country: "Portugal",
            code: "PT",
            liability: 15900,
            color: "#22262a",
        },
        {
            country: "UAE",
            code: "AE",
            liability: 0,
            color: "#a8d5ba",
        },
        {
            country: "Thailand",
            code: "TH",
            liability: 2100,
            color: "#22262a",
        },
    ],
    breakdownData = [
        {
            country: "Portugal",
            countryCode: "PT",
            incomeSource: "Freelance",
            taxableAmount: 85000,
            taxRate: 18.7,
            liability: 15900,
        },
        {
            country: "Thailand",
            countryCode: "TH",
            incomeSource: "Consulting",
            taxableAmount: 32000,
            taxRate: 6.5,
            liability: 2080,
        },
        {
            country: "USA",
            countryCode: "US",
            incomeSource: "Employment",
            taxableAmount: 15000,
            taxRate: 56.7,
            liability: 8505,
        },
    ],
    recommendations = [
        {
            id: 1,
            type: "tax_optimization",
            title: "Tax Optimization",
            description:
                "Save approx. $3,200 by shifting your tax residency to Dubai.",
            savings: 3200,
            details:
                "UAE offers no personal income tax on employment income. By establishing tax residency in Dubai, you can reduce your overall tax liability significantly. This requires maintaining a UAE residence and spending 183+ days in the country.",
            actionItems: [
                "Establish tax residency in UAE",
                "Rent a property in Dubai",
                "Document your 183+ day stay",
            ],
            actionButton: "Compare Scenarios",
            icon: "Zap",
        },
        {
            id: 2,
            type: "deduction",
            title: "Home Office Deduction",
            description: "Claim $2,400 annually as work-from-home expenses.",
            savings: 2400,
            details:
                "As a freelancer/remote worker, you can deduct a portion of your home expenses. Calculate your home office area as a percentage of your total home and claim that percentage of rent, utilities, and internet.",
            actionItems: [
                "Measure your home office area",
                "Calculate home percentage",
                "Keep utility and rent receipts",
            ],
            actionButton: "Learn More",
            icon: "Home",
        },
        {
            id: 3,
            type: "retirement",
            title: "Retirement Savings",
            description:
                "Open a retirement account to defer taxes on $15,000 income.",
            savings: 4500,
            details:
                "Contributing to a tax-advantaged retirement account reduces your taxable income. Both Solo 401(k) and SEP IRA are available for self-employed individuals and can significantly lower your tax burden.",
            actionItems: [
                "Compare 401(k) vs SEP IRA",
                "Open account with provider",
                "Set up automatic contributions",
            ],
            actionButton: "Open Account",
            icon: "TrendingUp",
        },
    ],
    residencyData = {
        primaryCountry: "Portugal",
        primaryCountryCode: "PT",
        daysSpent: 170,
        threshold: 183,
        daysRemaining: 13,
        riskLevel: "warning", // 'safe', 'warning', 'critical'
        riskMessage:
            "You have spent 170 days in Portugal. You are approaching the 183-day tax residency threshold, which may trigger full tax liability on worldwide income.",
        details:
            "In many countries, if you spend 183+ days in a single location, you become a tax resident and must pay tax on worldwide income. Portugal applies this rule, so be cautious about extending your stay beyond this threshold.",
        countries: [
            {
                name: "Portugal",
                code: "PT",
                days: 170,
                dates: "Apr 1 - Oct 2",
            },
            {
                name: "Thailand",
                code: "TH",
                days: 90,
                dates: "Oct 3 - Dec 31",
            },
            {
                name: "Mexico",
                code: "MX",
                days: 105,
                dates: "Jan 1 - Apr 15",
            },
        ],
    },
}) {
    // export default function Step3({
    //     auth,
    //     result,
    //     comparisonData,
    //     breakdownData,
    //     recommendations,
    //     residencyData,
    // }) {
    const isAuthenticated = auth?.user;

    const handleRecalculate = () => {
        window.location.href = route("tax-calculator.index");
    };

    // If user is authenticated, show dashboard layout
    if (isAuthenticated) {
        return (
            <div className="flex h-screen bg-light">
                {/* Sidebar */}
                <Sidebar user={auth.user} />

                {/* Main Content */}
                <div className="flex-1 ml-64 flex flex-col">
                    {/* Page Content */}
                    <main className="flex-1 overflow-y-auto">
                        <div className="max-w-6xl mx-auto px-6 md:px-8 py-12">
                            {/* Progress Section */}
                            <div className="mb-12 flex justify-between items-end">
                                <div>
                                    <h1 className="text-4xl md:text-5xl font-bold text-primary mb-2">
                                        Tax Calculation Results
                                    </h1>
                                    <p className="text-lg text-gray">
                                        Step 3 of 3: Your personalized tax
                                        analysis
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
                            <ResidencyRiskAlert residencyData={residencyData} />

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
                </div>
            </div>
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
                    <ResidencyRiskAlert residencyData={residencyData} />

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
