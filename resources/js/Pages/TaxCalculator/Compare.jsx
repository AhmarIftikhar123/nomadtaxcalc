"use client";

import React, { useState, useMemo } from "react";
import { Head, Link, usePage } from "@inertiajs/react";
import Joyride, { STATUS } from "react-joyride";
import { useCompareTour } from "@/hooks/useCompareTour";
import { useResultsTour } from "@/hooks/useResultsTour";
import TaxCalculatorLayout from "@/Layouts/TaxCalculatorLayout";
import ScenarioPanel from "@/Components/ScenarioComparison/ScenarioPanel";
import WinnerBanner from "@/Components/ScenarioComparison/WinnerBanner";
import ComparisonMetrics from "@/Components/ScenarioComparison/ComparisonMetrics";
import TaxByCountryBars from "@/Components/ScenarioComparison/TaxByCountryBars";
import ComparisonTable from "@/Components/ScenarioComparison/ComparisonTable";
import ResidencyRiskComparison from "@/Components/ScenarioComparison/ResidencyRiskComparison";
// import SmartRecommendations from "@/Components/TaxCalculator/SmartRecommendations";
import Tooltip from "@/Components/Ui/Tooltip";
import { Zap, Loader2, Printer, ArrowLeft, HelpCircle } from "lucide-react";
import web from "@/libs/axios";

// ─── helpers ──────────────────────────────────────────────────────────────────

function fmt(amount, currency = "USD") {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency,
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
}

function deepClone(obj) {
    return JSON.parse(JSON.stringify(obj));
}

// ─── Mobile tabs ──────────────────────────────────────────────────────────────

const TABS = ["Build", "Results", "Risk"];

// ─── component ────────────────────────────────────────────────────────────────

export default function Compare({
    countries = [],
    states = [],
    currencies = [],
    availableYears = [],
    taxTypes = [],
    prefillStep1 = null,
    prefillPeriods = [],
}) {
    const { auth } = usePage().props;

    // ─── Onboarding Tour (Comparison) ─────────────────────────────
    // isDefault: true if user hasn't set their actual profile data
    const isDefaultData = !prefillStep1 || !prefillStep1.citizenship_country_id;
    const {
        steps: tourSteps,
        tourActive,
        tourKey,
        startTour,
        handleTourCallback,
    } = useCompareTour(isDefaultData);

    const {
        steps: resultsTourSteps,
        tourActive: resultsTourActive,
        tourKey: resultsTourKey,
        startResultsTour,
        handleTourCallback: handleResultsTourCallback,
    } = useResultsTour();

    // ── Step 1 state (shared between both scenarios) ─────────────────────────
    const [step1, setStep1] = useState(() => {
        if (prefillStep1) return { ...prefillStep1 };
        return {
            annual_income: 100000,
            currency: "USD",
            citizenship_country_id: "",
            citizenship_country_code: "",
            citizenship_country_name: "",
            tax_year: availableYears[0] || 2026,
            domicile_state_id: null,
        };
    });

    // ── Country options for Select dropdown ──────────────────────────────────
    const countryOptions = useMemo(
        () =>
            countries.map((c) => ({
                value: c.id,
                label: c.name,
                code: c.code, // getCountries() returns 'code' (iso_code alias)
                tax_residency_days: c.tax_residency_days || 183,
            })),
        [countries],
    );

    // ── Scenario periods ─────────────────────────────────────────────────────
    const [periodsA, setPeriodsA] = useState(() => {
        if (prefillPeriods.length > 0) {
            return prefillPeriods.map((p) => ({
                country_id: p.country_id,
                country_code: p.country_code || "",
                country_name: p.country_name || "",
                days_spent: Number(p.days_spent) || 0,
                tax_residency_days: p.tax_residency_days || 183,
            }));
        }
        return [];
    });

    const [periodsB, setPeriodsB] = useState(() => {
        // Clone A as starting point for B
        if (prefillPeriods.length > 0) {
            return prefillPeriods.map((p) => ({
                country_id: p.country_id,
                country_code: p.country_code || "",
                country_name: p.country_name || "",
                days_spent: Number(p.days_spent) || 0,
                tax_residency_days: p.tax_residency_days || 183,
            }));
        }
        return [];
    });

    // ── Comparison results ───────────────────────────────────────────────────
    const [results, setResults] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    // ── Mobile tab state ─────────────────────────────────────────────────────
    const [activeTab, setActiveTab] = useState("Build");
    const [activeScenario, setActiveScenario] = useState("A"); // mobile toggle

    // ── Period handlers ──────────────────────────────────────────────────────
    const makeHandlers = (setPeriods) => ({
        update: (idx, field, value) => {
            setPeriods((prev) => {
                const next = [...prev];
                next[idx] = { ...next[idx], [field]: value };
                return next;
            });
        },
        remove: (idx) => {
            setPeriods((prev) => prev.filter((_, i) => i !== idx));
        },
        add: (countryData) => {
            setPeriods((prev) => [...prev, { ...countryData, days_spent: 0 }]);
        },
    });

    const handlersA = makeHandlers(setPeriodsA);
    const handlersB = makeHandlers(setPeriodsB);

    // ── Compare action ───────────────────────────────────────────────────────
    const handleCompare = async () => {
        setLoading(true);
        setError(null);

        try {
            const response = await web.post(
                route("tax-calculator.compare.run"),
                {
                    step1,
                    scenarioA: periodsA,
                    scenarioB: periodsB,
                },
            );

            // console.log(response);
            setResults(response.data);
            setActiveTab("Results");
            
            // Fire results tour shortly after tab switch
            setTimeout(() => {
                startResultsTour();
            }, 600);
        } catch (error) {
            console.error(error);

            if (error.response?.status === 422 && error.response.data?.errors) {
                // Handle validation errors from ScenarioComparisonRequest
                const errors = error.response.data.errors;
                const errorMessages = Object.values(errors).flat().join(" ");
                setError(errorMessages);
            } else if (error.response) {
                // Server responded but with error status
                setError(
                    error.response.data?.message ||
                        "Comparison failed. Please try again.",
                );
            } else {
                // Real network error
                setError(
                    "Network error. Please check your connection and try again.",
                );
            }
        } finally {
            setLoading(false);
        }
    };

    const currency = step1.currency || "USD";

    // Winner result for recommendations
    const winnerResult = results
        ? results.diff?.winner === "A"
            ? results.resultA
            : results.resultB
        : null;

    // ── Can compare? ─────────────────────────────────────────────────────────
    const totalA = periodsA.reduce(
        (s, p) => s + (Number(p.days_spent) || 0),
        0,
    );
    const totalB = periodsB.reduce(
        (s, p) => s + (Number(p.days_spent) || 0),
        0,
    );
    const canCompare =
        periodsA.length > 0 &&
        periodsB.length > 0 &&
        step1.citizenship_country_id &&
        step1.annual_income > 0;
    const joyrideStyles = {
        options: {
            primaryColor: "#1a1a2e",
            zIndex: 10000,
            arrowColor: "#fff",
            backgroundColor: "#fff",
            textColor: "#374151",
            overlayColor: "rgba(0,0,0,0.45)",
        },
        tooltip: {
            borderRadius: "12px",
            padding: "20px 24px",
            fontFamily: "inherit",
            fontSize: "14px",
            boxShadow: "0 20px 60px rgba(0,0,0,0.2)",
        },
        tooltipTitle: {
            fontSize: "15px",
            fontWeight: "700",
            marginBottom: "8px",
            color: "#1a1a2e",
        },
        buttonNext: {
            backgroundColor: "#1a1a2e",
            color: "#fff",
            borderRadius: "8px",
            padding: "8px 18px",
            fontWeight: "600",
            fontSize: "13px",
        },
        buttonBack: {
            color: "#6b7280",
            fontWeight: "600",
            fontSize: "13px",
        },
        buttonSkip: {
            color: "#9ca3af",
            fontSize: "12px",
        },
        buttonClose: {
            top: "12px",
            right: "12px",
        },
    };

    return (
        <TaxCalculatorLayout title="Compare Scenarios">
            <Head title="Compare Scenarios" />

            {/* ─── React Joyride (Comparison Builder) ────────────────── */}
            <Joyride
                key={tourKey}
                steps={tourSteps}
                run={tourActive}
                continuous
                scrollToFirstStep
                showProgress
                showSkipButton
                callback={(data) => {
                    if (
                        data.status === STATUS.FINISHED ||
                        data.status === STATUS.SKIPPED
                    ) {
                        handleTourCallback(data);
                    }
                }}
                styles={joyrideStyles}
            />

            {/* ─── React Joyride (Results Phase) ─────────────────────── */}
            <Joyride
                key={`rt-${resultsTourKey}`}
                steps={resultsTourSteps}
                run={resultsTourActive}
                continuous
                scrollToFirstStep
                showProgress
                showSkipButton
                callback={handleResultsTourCallback}
                styles={joyrideStyles}
            />

            <div className="max-w-7xl mx-auto space-y-6">
                {/* ── Hero Header ─────────────────────────────────────────── */}
                <div className="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div className="flex items-start gap-4">
                        <div data-tour="compare-back" className="mt-0.5">
                            <Tooltip
                                text="Back to Previous Step"
                                position="bottom"
                            >
                                <Link
                                    href={route("tax-calculator.index", {
                                        scenario_comparison: "true",
                                    })}
                                    className="p-2 flex items-center justify-center rounded-lg border border-border-gray hover:bg-primary hover:text-light text-primary transition-colors duration-200"
                                    aria-label="Go to previous step"
                                >
                                    <ArrowLeft className="w-6 h-6" />
                                </Link>
                            </Tooltip>
                        </div>
                        <div className="flex-1">
                            <div className="flex flex-wrap items-center gap-3">
                                <h1 className="text-2xl md:text-3xl font-extrabold text-primary leading-tight">
                                    What if you travelled{" "}
                                    <span className="text-green-600">
                                        differently
                                    </span>
                                    ?
                                </h1>
                                {/* Tour trigger button */}
                                <button
                                    type="button"
                                    onClick={startTour}
                                    className="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-primary border border-border-gray rounded-lg hover:bg-primary hover:text-light transition-colors duration-200"
                                >
                                    <HelpCircle className="w-4 h-4" />
                                    Quick Tour
                                </button>
                            </div>
                            <p className="text-sm text-gray mt-1">
                                Adjust days per country below — compare full tax
                                results instantly.
                            </p>
                        </div>
                    </div>

                    {step1.annual_income > 0 && (
                        <div className="bg-primary/5 text-primary border border-primary/10 px-5 py-3 rounded-xl text-right flex-shrink-0">
                            <p className="text-lg font-extrabold">
                                {fmt(step1.annual_income, currency)}
                            </p>
                            <p className="text-xs text-primary/60 font-medium">
                                {step1.citizenship_country_name || "—"} · Tax
                                Year {step1.tax_year}
                            </p>
                        </div>
                    )}
                </div>

                {/* ── Mobile Tab Bar (visible < md) ───────────────────────── */}
                <div className="md:hidden flex bg-white rounded-xl border border-border-gray overflow-hidden">
                    {TABS.map((tab) => (
                        <button
                            key={tab}
                            onClick={() => setActiveTab(tab)}
                            className={`flex-1 py-3 text-sm font-bold transition-colors ${
                                activeTab === tab
                                    ? "bg-primary text-light"
                                    : "text-gray hover:bg-light"
                            }`}
                        >
                            {tab}
                        </button>
                    ))}
                </div>

                {/* ── BUILD SECTION ───────────────────────────────────────── */}
                <div
                    className={`${activeTab !== "Build" ? "hidden md:block" : ""}`}
                >
                    {/* Mobile scenario toggle */}
                    <div className="md:hidden flex bg-white rounded-xl border border-border-gray overflow-hidden mb-4">
                        {["A", "B"].map((s) => (
                            <button
                                key={s}
                                onClick={() => setActiveScenario(s)}
                                className={`flex-1 py-2.5 text-sm font-bold transition-colors ${
                                    activeScenario === s
                                        ? "bg-primary text-light"
                                        : "text-gray hover:bg-light"
                                }`}
                            >
                                Scenario {s} ({s === "A" ? "Current" : "New"})
                            </button>
                        ))}
                    </div>

                    {/* Desktop: side by side | Mobile: toggled */}
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div
                            data-tour="scenario-a"
                            className={`${activeScenario !== "A" ? "hidden md:block" : ""}`}
                        >
                            <ScenarioPanel
                                label="Scenario A"
                                badge="CURRENT"
                                periods={periodsA}
                                onUpdatePeriod={handlersA.update}
                                onRemovePeriod={handlersA.remove}
                                onAddPeriod={handlersA.add}
                                countries={countryOptions}
                                accentColor="border-green-500"
                            />
                        </div>
                        <div
                            data-tour="scenario-b"
                            className={`${activeScenario !== "B" ? "hidden md:block" : ""}`}
                        >
                            <ScenarioPanel
                                label="Scenario B"
                                badge="NEW"
                                periods={periodsB}
                                onUpdatePeriod={handlersB.update}
                                onRemovePeriod={handlersB.remove}
                                onAddPeriod={handlersB.add}
                                countries={countryOptions}
                                accentColor="border-blue-500"
                            />
                        </div>
                    </div>

                    {/* Compare CTA */}
                    <div
                        data-tour="compare-run"
                        className="bg-primary/5 border border-primary/10 rounded-xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4 mt-5"
                    >
                        <p className="text-sm text-primary/70 font-medium">
                            Adjust days above · click compare to see full tax
                            impact
                        </p>
                        <Tooltip
                            text={
                                !canCompare
                                    ? "Add countries to both scenarios first"
                                    : "Run both scenarios through the tax engine"
                            }
                            position="top"
                        >
                            <button
                                onClick={handleCompare}
                                disabled={!canCompare || loading}
                                className="inline-flex items-center gap-2 px-8 py-4 bg-green-500 text-white font-extrabold rounded-xl hover:bg-green-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed text-sm whitespace-nowrap"
                            >
                                {loading ? (
                                    <Loader2 className="w-5 h-5 animate-spin" />
                                ) : (
                                    <Zap className="w-5 h-5" />
                                )}
                                {loading ? "Comparing…" : "Compare Scenarios →"}
                            </button>
                        </Tooltip>
                    </div>

                    {/* Error */}
                    {error && (
                        <div className="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm font-medium">
                            {error}
                        </div>
                    )}
                </div>

                {/* ── RESULTS SECTION ─────────────────────────────────────── */}
                {results && (
                    <div
                        className={`space-y-6 ${activeTab !== "Results" ? "hidden md:block" : ""}`}
                    >
                        <div data-tour="results-summary" className="space-y-6">
                            <WinnerBanner diff={results.diff} currency={currency} />
                            <ComparisonMetrics
                                resultA={results.resultA}
                                resultB={results.resultB}
                                diff={results.diff}
                                currency={currency}
                            />
                            <TaxByCountryBars
                                perCountry={results.diff?.perCountry || []}
                                currency={currency}
                            />
                            <ComparisonTable
                                perCountry={results.diff?.perCountry || []}
                                resultA={results.resultA}
                                resultB={results.resultB}
                                diff={results.diff}
                                currency={currency}
                            />
                        </div>
                        {/* ── RISK TAB (mobile only, desktop shows inline) ────────── */}
                        {results && (
                            <div
                                data-tour="results-risk"
                                className={`${activeTab !== "Risk" ? "hidden md:block" : ""}`}
                            >
                                <ResidencyRiskComparison
                                    resultA={results.resultA}
                                    resultB={results.resultB}
                                />
                            </div>
                        )}
                        {/* Recommendations from winner */}
                        {/* {winnerResult?.recommendations?.length > 0 && (
                            <SmartRecommendations
                                recommendations={winnerResult.recommendations}
                                currency={currency}
                            />
                        )} */}

                        {/* Action buttons */}
                        <div className="flex flex-col sm:flex-row gap-3">
                            <Tooltip text="Print or save as PDF" position="top">
                                <button
                                    onClick={() => window.print()}
                                    className="inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-border-gray text-primary font-bold rounded-xl hover:border-primary transition-all text-sm w-full sm:w-auto"
                                >
                                    <Printer className="w-4 h-4" />
                                    Print Report
                                </button>
                            </Tooltip>
                            <Tooltip
                                text="Go back to the main calculator"
                                position="top"
                            >
                                <a
                                    href={route("tax-calculator.index")}
                                    className="inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-border-gray text-gray font-bold rounded-xl hover:border-primary hover:text-primary transition-all text-sm w-full sm:w-auto"
                                >
                                    <ArrowLeft className="w-4 h-4" />
                                    Back to Calculator
                                </a>
                            </Tooltip>
                        </div>
                    </div>
                )}
            </div>
        </TaxCalculatorLayout>
    );
}
