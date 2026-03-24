"use client";

import React, { useState, useMemo, useEffect } from "react";
import { useForm, usePage, Link, router } from "@inertiajs/react";
import Joyride, { STATUS } from "react-joyride";
import { useTaxTour } from "@/hooks/useTaxTour";
import {
    RotateCcw,
    Download,
    ArrowLeft,
    Save,
    Check,
    TriangleAlert,
    Mail,
    Link2,
    HelpCircle,
} from "lucide-react";
import FlashMessage from "@/Components/ui/FlashMessage";
import TaxCalculatorLayout from "@/Layouts/TaxCalculatorLayout";
import Step1Form from "@/Components/TaxCalculator/Step1Form";
import Form1Summary from "@/Components/TaxCalculator/Form1Summary";
import Step2Form from "@/Components/TaxCalculator/Step2Form";
import ResultMetricsCards from "@/Components/TaxCalculator/ResultMetricsCards";
import ResidencyRiskAlert from "@/Components/TaxCalculator/ResidencyRiskAlert";
import TaxLiabilityComparison from "@/Components/TaxCalculator/TaxLiabilityComparison";
import SmartRecommendations from "@/Components/TaxCalculator/SmartRecommendations";
import TaxCalculationFlow from "@/Components/TaxCalculator/TaxCalculationFlow";
import DetailedTaxBreakdown from "@/Components/TaxCalculator/DetailedTaxBreakdown";
import TreatiesApplied from "@/Components/TaxCalculator/TreatiesApplied";
import FEIEStatus from "@/Components/TaxCalculator/FEIEStatus";
import ResidencyInsights from "@/Components/TaxCalculator/ResidencyInsights";
import DisclaimerBanner from "@/Components/ui/DisclaimerBanner";
import Tooltip from "@/Components/UI/Tooltip";
import ShareLinkModal from "@/Components/Ui/ShareLinkModal";

export default function TaxCalculatorIndex({
    countries,
    states,
    currencies,
    availableYears,
    taxTypes,
    savedStep1Data,
    savedResidencyPeriods,
    calculationResult: initialResult,
    currentStep: initialStep,
    editingCalculationId,
}) {
    const { auth, flash } = usePage().props;

    // Detect ?scenario_comparison=true || ?scenario_comparison=1 in the URL
    const isScenarioComparisonMode = useMemo(() => {
        if (typeof window === "undefined") return false;
        const params = new URLSearchParams(window.location.search);
        return params.get("scenario_comparison") === "true";
    }, []);

    // ─── Step State ──────────────────────────────────────────────
    const [step, setStep] = useState(initialStep || 1);
    const [result, setResult] = useState(initialResult || null);
    const [activeTab, setActiveTab] = useState("summary");
    const [calculationError, setCalculationError] = useState(null);
    const [isSaving, setIsSaving] = useState(false);
    const [isSendingEmail, setIsSendingEmail] = useState(false);
    const [emailSent, setEmailSent] = useState(false);
    const [isGeneratingLink, setIsGeneratingLink] = useState(false);
    const [linkCopied, setLinkCopied] = useState(false);
    const [shareExpiry, setShareExpiry] = useState(null);
    const [shareUrl, setShareUrl] = useState(null);
    const [showShareModal, setShowShareModal] = useState(false);



    // Track the saved DB record ID so subsequent saves UPDATE instead of INSERT.
    // Starts as editingCalculationId (set when user loads ?calculation_id=X),
    // then updates from flash.saved_calculation_id after every successful save.
    const [savedCalculationId, setSavedCalculationId] = useState(
        editingCalculationId ?? null,
    );

    // Pick up the ID returned by the backend after each save
    useEffect(() => {
        if (flash?.saved_calculation_id) {
            setSavedCalculationId(flash.saved_calculation_id);
        }
    }, [flash?.saved_calculation_id]);

    // Pick up the share URL returned by the backend and open the modal
    useEffect(() => {
        if (flash?.share_url) {
            const exp = new Date();
            exp.setDate(exp.getDate() + 30);
            setShareUrl(flash.share_url);
            setShareExpiry(
                exp.toLocaleDateString("en-US", {
                    month: "long",
                    day: "numeric",
                    year: "numeric",
                }),
            );
            setShowShareModal(true);
        }
    }, [flash?.share_url]);

    // Track whether the current result has been saved
    const [hasSaved, setHasSaved] = useState(!!editingCalculationId);

    const tabs = [
        { id: "summary", label: "Summary" },
        { id: "residency", label: "Residency" },
        { id: "treaties", label: "Treaties & FEIE" },
        { id: "breakdown", label: "Breakdown" },
        { id: "recommendations", label: "Recommendations" },
    ];

    // ─── Single useForm for ALL steps ────────────────────────────
    const { data, setData, post, processing, errors, reset } = useForm({
        // Step 1 fields
        annual_income: savedStep1Data?.annual_income ?? "",
        currency: savedStep1Data?.currency ?? "USD",
        citizenship_country_id: savedStep1Data?.citizenship_country_id ?? "",
        domicile_state_id: savedStep1Data?.domicile_state_id ?? "",
        tax_year:
            savedStep1Data?.tax_year ??
            availableYears?.[0] ??
            new Date().getFullYear(),
        // Step 2 fields
        residency_periods:
            savedResidencyPeriods && savedResidencyPeriods.length > 0
                ? savedResidencyPeriods
                : [],
    });

    // ─── Onboarding Tour ──────────────────────────────────────────
    // Placed after useForm so `data` is available.
    // hasPeriods gates the tax-types step — that DOM target only exists
    // once a ResidencyPeriodItem card is rendered.
    const hasPeriods = (data.residency_periods ?? []).length > 0;
    const { steps: tourSteps, tourActive, tourKey, startTour, handleTourCallback } =
        useTaxTour(step, hasPeriods);

    // ─── Resolve citizenship country info for Form1Summary ───────
    const citizenshipSummary = useMemo(() => {
        if (!data.citizenship_country_id) {
            return {
                citizenship_country_code:
                    savedStep1Data?.citizenship_country_code ?? "",
                citizenship_country_name:
                    savedStep1Data?.citizenship_country_name ?? "",
            };
        }
        const country = countries.find(
            (c) => c.id === Number(data.citizenship_country_id),
        );
        return {
            citizenship_country_code: country?.code ?? "",
            citizenship_country_name: country?.name ?? "",
        };
    }, [data.citizenship_country_id, countries, savedStep1Data]);

    // ─── Step titles ─────────────────────────────────────────────
    const stepConfig = {
        1: {
            title: "Income & Citizenship",
            subtitle: "Step 1 of 3: Let's start with the basics",
            progress: 0,
        },
        2: {
            title: "Residency Details",
            subtitle: "Step 2 of 3: Where did you live this year?",
            progress: 40,
        },
        3: {
            title: `Tax Calculation Results — ${result?.tax_year || data.tax_year || 2026}`,
            subtitle: "Step 3 of 3: Your personalized tax analysis",
            progress: 100,
        },
    };

    const current = stepConfig[step];

    // ─── Step 1 Submit ───────────────────────────────────────────
    const handleStep1Submit = (e) => {
        e.preventDefault();
        post(route("tax-calculator.step-1"), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                if (isScenarioComparisonMode) {
                    // Redirect to the compare page — session data is already saved
                    router.visit(route("tax-calculator.compare"));
                } else {
                    setStep(2);
                }
            },
        });
    };

    // ─── Step 2 Submit ───────────────────────────────────────────
    const handleStep2Submit = () => {
        setCalculationError(null);
        setHasSaved(false);
        setEmailSent(false);
        post(route("tax-calculator.step-2.store"), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                // Server calculated taxes, extract result and advance to step 3
                const result =
                    page.props.calculationResult ||
                    page.props.flash?.calculationResult;
                setResult(result);
                setStep(3);
            },
            onError: (err) => {
                if (err.calculation) {
                    setCalculationError(err.calculation);
                    setStep(3); // Go to step 3 to show the error card
                }
            },
        });
    };

    // ─── Navigation ──────────────────────────────────────────────
    const handleBack = () => {
        setStep((prev) => Math.max(1, prev - 1));
        window.scrollTo({ top: 0, behavior: "smooth" });
    };

    const handleRecalculate = () => {
        setStep(1);
        window.scrollTo({ top: 0, behavior: "smooth" });
    };

    // ─── Save / Update Calculation (auth users only) ──────────────────────────
    const handleSave = () => {
        setIsSaving(true);
        router.post(
            route("tax-calculator.save"),
            { calculation_id: savedCalculationId ?? null },
            {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => {
                    setIsSaving(false);
                    setHasSaved(true);
                },
            },
        );
    };

    // ─── Email Results (auth users only) ──────────────
    const handleEmailResults = () => {
        setIsSendingEmail(true);
        router.post(
            route("tax-calculator.email-results"),
            { calculation_id: savedCalculationId },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => setEmailSent(true),
                onFinish: () => setIsSendingEmail(false),
            },
        );
    };

    // ─── Share Link (auth users only) ─────────────────
    const handleShareLink = () => {
        setIsGeneratingLink(true);
        router.post(
            route("tax-calculator.generate-link"),
            { calculation_id: savedCalculationId },
            {
                preserveState: true,
                preserveScroll: true,
                // Modal opens reactively via the useEffect watching flash.share_url
                onFinish: () => setIsGeneratingLink(false),
            },
        );
    };

    // ─── Render ──────────────────────────────────────────────────
    return (
        <TaxCalculatorLayout
            title={current.title}
            onRecalculate={step === 3 ? handleRecalculate : undefined}
        >
            {/* ─── React Joyride ─────────────────────────────── */}
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
                styles={{
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
                }}
            />

            <div
                className={`mx-auto px-2 sm:px-6 md:px-8 py-6 md:py-12 ${step === 3 ? "max-w-6xl" : step === 2 ? "max-w-4xl" : "max-w-[1200px]"}`}
            >
                {/* ─── Progress Section ──────────────────────── */}
                {!isScenarioComparisonMode && (
                    <div className="mb-12 flex flex-col md:flex-row md:justify-between md:items-end items-start">
                        <div className="flex-1">
                            <div className="flex items-center gap-4 mb-2">
                                {step > 1 && (
                                    <button
                                        type="button"
                                        onClick={handleBack}
                                        className="p-2 -ml-2 rounded-lg border border-border-gray hover:bg-primary hover:text-light text-primary transition-colors duration-200"
                                        aria-label="Go to previous step"
                                    >
                                        <ArrowLeft className="w-6 h-6" />
                                    </button>
                                )}
                                <h1 className="text-4xl md:text-5xl font-bold text-primary">
                                    {current.title}
                                </h1>
                            </div>
                            <p className="text-lg text-gray">
                                {current.subtitle}
                            </p>
                        </div>
                        <div className="flex items-center gap-4 text-right w-full md:w-auto mt-4 md:mt-0">
                            {/* Tour trigger button */}
                            <button
                                type="button"
                                onClick={startTour}
                                className="flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-primary border border-border-gray rounded-lg hover:bg-primary hover:text-light transition-colors duration-200"
                                title="Take a quick tour of this step"
                            >
                                <HelpCircle className="w-4 h-4" />
                                Quick Tour
                            </button>
                            <div className="w-full md:w-48">
                                <p className="text-sm font-bold text-primary mb-2">
                                    {current.progress}% Completed
                                </p>
                                <div className="w-full h-1.5 bg-border-gray rounded-full overflow-hidden">
                                    <div
                                        className="h-full bg-primary transition-all duration-500"
                                        style={{ width: `${current.progress}%` }}
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* ═══════════════ STEP 1 ═══════════════ */}
                {step === 1 && (
                    <div className="bg-white rounded-xl border border-border-gray p-5 sm:p-8 md:p-12 shadow-sm">
                        <h2 className="text-4xl md:text-5xl font-bold text-primary mb-4">
                            Let's start with the basics
                        </h2>
                        <p className="text-lg text-gray mb-10">
                            {isScenarioComparisonMode
                                ? "Enter your income and citizenship — then we'll build your comparison scenarios."
                                : "To accurately estimate your tax liability, we need to know your annual earnings and your primary country of citizenship."}
                        </p>

                        <form onSubmit={handleStep1Submit}>
                            <Step1Form
                                data={data}
                                setData={setData}
                                errors={errors}
                                countries={countries}
                                states={states}
                                currencies={currencies}
                                availableYears={availableYears}
                                processing={processing}
                                isScenarioComparisonMode={
                                    isScenarioComparisonMode
                                }
                            />
                        </form>
                    </div>
                )}

                {/* ═══════════════ STEP 2 ═══════════════ */}
                {step === 2 && (
                    <>
                        {/* Form1 Summary */}
                        <Form1Summary
                            formData={{
                                annual_income: data.annual_income,
                                currency: data.currency,
                                tax_year: data.tax_year,
                                ...citizenshipSummary,
                            }}
                        />

                        {/* Step2 Form Card */}
                        <div className="bg-white rounded-xl border border-border-gray p-1 sm:p-8 md:p-12 shadow-sm">
                            <Step2Form
                                data={data}
                                setData={setData}
                                errors={errors}
                                processing={processing}
                                onSubmit={handleStep2Submit}
                                onBack={handleBack}
                                countries={countries}
                                states={states}
                                taxTypes={taxTypes}
                                taxYear={data.tax_year}
                                step1Currency={data.currency || "USD"}
                            />
                        </div>
                    </>
                )}

                {/* ═══════════════ STEP 3 ═══════════════ */}
                {step === 3 && (
                    <>
                        <DisclaimerBanner />

                        {/* Flash Messages */}
                        <FlashMessage type="success" message={flash?.success} />
                        <FlashMessage type="error" message={flash?.error} />

                        {calculationError && (
                            <div className="bg-red-50 border-2 border-red-200 rounded-xl p-8 md:p-12 shadow-sm text-center max-w-3xl mx-auto mt-12">
                                <TriangleAlert className="w-16 h-16 text-red-500 mx-auto mb-6" />
                                <h2 className="text-3xl font-bold text-red-900 mb-4">
                                    Calculation Failed
                                </h2>
                                <p className="text-lg text-red-700 mb-8 max-w-xl mx-auto">
                                    {calculationError}
                                </p>
                                <button
                                    onClick={() => {
                                        setCalculationError(null);
                                        setStep(2);
                                        window.scrollTo({
                                            top: 0,
                                            behavior: "smooth",
                                        });
                                    }}
                                    className="px-8 py-4 bg-primary hover:bg-dark text-light font-bold rounded-lg transition-all"
                                >
                                    Try Again
                                </button>
                            </div>
                        )}

                        {!calculationError && result && (
                            <>
                                {/* Tabs Navigation */}
                                <div
                                    data-tour="step3-tabs"
                                    className="flex overflow-x-auto overflow-y-hidden gap-2 mb-8 pb-2 border-b-2 border-border-gray no-scrollbar"
                                >
                                    {tabs.map((tab) => (
                                        <button
                                            key={tab.id}
                                            onClick={() => setActiveTab(tab.id)}
                                            className={`whitespace-nowrap px-6 py-3 font-bold rounded-t-lg transition-colors border-2 border-b-0 -mb-[2px] ${
                                                activeTab === tab.id
                                                    ? "bg-primary text-light border-primary"
                                                    : "bg-light text-gray hover:bg-gray/10 border-transparent"
                                            }`}
                                        >
                                            {tab.label}
                                        </button>
                                    ))}
                                </div>

                                {/* Tab Content: Summary */}
                                {activeTab === "summary" && (
                                    <div className="space-y-6">
                                        <div data-tour="step3-metrics">
                                            <ResultMetricsCards result={result} />
                                        </div>
                                        <div data-tour="step3-flow">
                                            <TaxCalculationFlow result={result} />
                                        </div>
                                    </div>
                                )}

                                {/* Tab Content: Residency */}
                                {activeTab === "residency" && (
                                    <div className="space-y-6">
                                        <ResidencyInsights
                                            residencyData={
                                                result.residency_data || []
                                            }
                                        />
                                        <ResidencyRiskAlert
                                            residencyData={
                                                result.residency_data || []
                                            }
                                        />
                                    </div>
                                )}

                                {/* Tab Content: Treaties & FEIE */}
                                {activeTab === "treaties" && (
                                    <div className="space-y-6">
                                        <TreatiesApplied
                                            treatiesApplied={
                                                result.treaties_applied
                                            }
                                            currency={result.currency}
                                        />
                                        <FEIEStatus
                                            feieResult={result.feie_result}
                                            citizenshipCountryCode={
                                                citizenshipSummary.citizenship_country_code
                                            }
                                            currency={result.currency}
                                            taxYear={result.tax_year}
                                        />
                                    </div>
                                )}

                                {/* Tab Content: Breakdown */}
                                {activeTab === "breakdown" && (
                                    <div className="space-y-6">
                                        <TaxLiabilityComparison
                                            comparisonData={
                                                result.comparison_data || []
                                            }
                                            currency={result.currency}
                                        />
                                        <DetailedTaxBreakdown
                                            breakdownData={
                                                result.breakdown_by_country ||
                                                []
                                            }
                                            currency={result.currency}
                                            taxYear={result.tax_year}
                                        />
                                    </div>
                                )}

                                {/* Tab Content: Recommendations */}
                                {activeTab === "recommendations" && (
                                    <div className="space-y-6">
                                        <SmartRecommendations
                                            recommendations={
                                                result.recommendations || []
                                            }
                                            currency={result.currency}
                                        />
                                    </div>
                                )}

                                {/* Action Buttons */}
                                <div
                                    data-tour="step3-login-cta"
                                    className="flex gap-4 justify-center flex-wrap mt-12 mb-6"
                                >
                                    <button
                                        type="button"
                                        onClick={handleRecalculate}
                                        className="px-8 py-4 border-2 border-primary text-primary font-bold rounded-lg hover:bg-primary hover:text-light transition-all flex items-center gap-2"
                                    >
                                        <RotateCcw className="w-5 h-5" />
                                        Start Over
                                    </button>

                                    {/* Save / Update — only for logged-in users */}
                                    {auth?.user && (
                                        <button
                                            type="button"
                                            onClick={handleSave}
                                            disabled={isSaving || hasSaved}
                                            className={`px-8 py-4 border-2 font-bold rounded-lg transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed ${
                                                hasSaved
                                                    ? "border-green-500 text-green-600"
                                                    : "border-primary text-primary hover:bg-primary hover:text-light"
                                            }`}
                                        >
                                            {hasSaved ? (
                                                <>
                                                    <Check className="w-5 h-5" />
                                                    Saved ✓
                                                </>
                                            ) : isSaving ? (
                                                <>
                                                    <Save className="w-5 h-5 animate-pulse" />
                                                    Saving...
                                                </>
                                            ) : (
                                                <>
                                                    <Save className="w-5 h-5" />
                                                    {editingCalculationId
                                                        ? "Update Calculation"
                                                        : "Save Calculation"}
                                                </>
                                            )}
                                        </button>
                                    )}

                                    {/* Email Results — auth only, requires saved calc */}
                                    {auth?.user && (
                                        <Tooltip
                                            text={
                                                !savedCalculationId
                                                    ? "Save your calculation first to unlock this"
                                                    : emailSent
                                                      ? "Results already sent to your email"
                                                      : "Send a full breakdown to your registered email"
                                            }
                                            position="top"
                                        >
                                            <button
                                                type="button"
                                                onClick={handleEmailResults}
                                                disabled={
                                                    isSendingEmail ||
                                                    emailSent ||
                                                    !savedCalculationId
                                                }
                                                className={`px-8 py-4 border-2 font-bold rounded-lg transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed ${
                                                    emailSent
                                                        ? "border-green-500 text-green-600"
                                                        : "border-primary text-primary hover:bg-primary hover:text-light"
                                                }`}
                                            >
                                                {emailSent ? (
                                                    <>
                                                        <Check className="w-5 h-5" />
                                                        Sent ✓
                                                    </>
                                                ) : isSendingEmail ? (
                                                    <>
                                                        <Mail className="w-5 h-5 animate-pulse" />
                                                        Sending...
                                                    </>
                                                ) : (
                                                    <>
                                                        <Mail className="w-5 h-5" />
                                                        Email Results
                                                    </>
                                                )}
                                            </button>
                                        </Tooltip>
                                    )}

                                    {/* Share Link — auth only, requires saved calc */}
                                    {auth?.user && (
                                        <Tooltip
                                            text={
                                                !savedCalculationId
                                                    ? "Save your calculation first to unlock this"
                                                    : isGeneratingLink
                                                      ? "Generating shareable link..."
                                                      : "Generate a read-only link valid for 30 days"
                                            }
                                            position="top"
                                        >
                                            <button
                                                type="button"
                                                onClick={handleShareLink}
                                                disabled={
                                                    isGeneratingLink ||
                                                    !savedCalculationId
                                                }
                                                className={`px-8 py-4 border-2 font-bold rounded-lg transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed ${
                                                    linkCopied
                                                        ? "border-green-500 text-green-600"
                                                        : "border-primary text-primary hover:bg-primary hover:text-light"
                                                }`}
                                            >
                                                {isGeneratingLink ? (
                                                    <>
                                                        <Link2 className="w-5 h-5 animate-pulse" />
                                                        Generating...
                                                    </>
                                                ) : (
                                                    <>
                                                        <Link2 className="w-5 h-5" />
                                                        Share Link
                                                    </>
                                                )}
                                            </button>
                                        </Tooltip>
                                    )}

                                    <button
                                        onClick={() => window.print()}
                                        className="px-8 py-4 bg-primary hover:bg-dark text-light font-bold rounded-lg transition-all flex items-center gap-2"
                                    >
                                        <Download className="w-5 h-5" />
                                        Print Results
                                    </button>
                                </div>

                                {/* Expiry & save-first warnings */}
                                <div className="flex flex-col items-center gap-2 mb-12">
                                    {auth?.user && !savedCalculationId && (
                                        <p className="text-sm text-gray flex items-center gap-1.5">
                                            <TriangleAlert className="w-4 h-4 text-amber-500" />
                                            Save your calculation to unlock
                                            Email &amp; Share features.
                                        </p>
                                    )}
                                    {shareExpiry && (
                                        <p className="text-sm text-amber-600 flex items-center gap-1.5">
                                            <TriangleAlert className="w-4 h-4" />
                                            Shareable link expires on{" "}
                                            {shareExpiry}.
                                        </p>
                                    )}
                                </div>

                                {/* Share Link Modal */}
                                <ShareLinkModal
                                    isOpen={showShareModal}
                                    onClose={() => setShowShareModal(false)}
                                    shareUrl={shareUrl}
                                    expiresOn={shareExpiry}
                                />
                            </>
                        )}
                    </>
                )}
            </div>
        </TaxCalculatorLayout>
    );
}
