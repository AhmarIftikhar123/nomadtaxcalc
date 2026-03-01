"use client";

import React, { useState, useMemo } from "react";
import {
    Search,
    Plus,
    CheckCircle2,
    TriangleAlert,
    ArrowRight,
    ChevronLeft,
} from "lucide-react";
import ResidencyPeriodItem from "./ResidencyPeriodItem";
import Select from "@/Components/Form/Select";
import Loader from "@/Components/ui/Loader";

const MONTHS = [
    "Jan",
    "Feb",
    "Mar",
    "Apr",
    "May",
    "Jun",
    "Jul",
    "Aug",
    "Sep",
    "Oct",
    "Nov",
    "Dec",
];

const COUNTRIES = [
    "United States",
    "United Kingdom",
    "Canada",
    "Australia",
    "Germany",
    "France",
    "Spain",
    "Italy",
    "Netherlands",
    "Belgium",
    "Switzerland",
    "Sweden",
    "Norway",
    "Denmark",
    "Portugal",
    "Thailand",
    "Mexico",
    "India",
    "Japan",
    "Singapore",
    "New Zealand",
    "Ireland",
    "Greece",
    "Austria",
    "Poland",
    "Czech Republic",
    "Hungary",
    "Romania",
    "Bulgaria",
];

export default function Step2Form({
    data,
    setData,
    errors,
    processing,
    onSubmit,
    onBack,
    countries = [],
    states = [],
    taxTypes = [],
    taxYear,
    step1Currency = "USD", // currency chosen in Step 1
}) {
    const [selectedCountry, setSelectedCountry] = useState(null);
    const [selectedStateId, setSelectedStateId] = useState("");
    const [daysSpent, setDaysSpent] = useState("");
    const [residencyPeriods, setResidencyPeriods] = useState(
        data.residency_periods || [],
    );
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Filter countries (exclude already-added ones) and format for Select component
    const countryOptions = useMemo(() => {
        return countries
            .filter((c) => !residencyPeriods.some((r) => r.country_id === c.id))
            .map((c) => ({ value: c.id.toString(), label: c.name }));
    }, [countries, residencyPeriods]);

    // Calculate total days
    const totalDays = useMemo(() => {
        return residencyPeriods.reduce(
            (sum, period) => sum + parseInt(period.days || 0),
            0,
        );
    }, [residencyPeriods]);

    const daysRemaining = 365 - totalDays;
    const isYearComplete = totalDays === 365;
    const hasErrors = totalDays > 365;

    const isOverLimit = useMemo(() => {
        if (!daysSpent) return false;
        return totalDays + parseInt(daysSpent) > 365;
    }, [totalDays, daysSpent]);

    // Calculate progress percentage
    const progressPercentage = Math.min((totalDays / 365) * 100, 100);

    const handleAddPeriod = () => {
        if (!selectedCountry || !daysSpent || parseInt(daysSpent) <= 0) {
            return;
        }

        if (totalDays + parseInt(daysSpent) > 365) {
            return;
        }

        // Find default income tax type
        const incomeTaxType = taxTypes.find((t) => t.key === "");

        const newPeriod = {
            id: Date.now(),
            country_id: selectedCountry.id,
            state_id: selectedCountry?.code === "US" ? selectedStateId : null,
            state_name:
                selectedCountry?.code === "US"
                    ? states.find((s) => s.id.toString() === selectedStateId)
                          ?.name
                    : null,
            country_name: selectedCountry.name,
            country_code: selectedCountry.code,
            country_tax_basis: selectedCountry.tax_basis ?? "worldwide",
            days: parseInt(daysSpent),
            local_income: "",
            local_income_currency: step1Currency, // default to step1 currency
            startMonth: 0,
            endMonth: 11,
            isTaxResident: parseInt(daysSpent) >= 183,
            selected_tax_types: incomeTaxType
                ? [
                      {
                          id: Date.now(),
                          tax_type_id: incomeTaxType.id.toString(),
                          custom_name: "",
                          amount_type: "percentage",
                          amount: "",
                          is_custom: false,
                      },
                  ]
                : [],
        };

        const updated = [...residencyPeriods, newPeriod];
        setResidencyPeriods(updated);
        setSelectedCountry(null);
        setSelectedStateId("");
        setDaysSpent("");
        setData("residency_periods", updated);
    };

    const handleRemovePeriod = (id) => {
        const updated = residencyPeriods.filter((p) => p.id !== id);
        setResidencyPeriods(updated);
        setData("residency_periods", updated);
    };

    const handleUpdatePeriod = (id, field, value) => {
        const updated = residencyPeriods.map((p) =>
            p.id === id ? { ...p, [field]: value } : p,
        );
        setResidencyPeriods(updated);
        setData("residency_periods", updated);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!isYearComplete || isSubmitting || processing) {
            return;
        }
        setIsSubmitting(true);
        onSubmit();

        // Let processing prop take over after a tiny delay
        setTimeout(() => setIsSubmitting(false), 1000);
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-8">
            {/* Fiscal Year Timeline */}
            <div className="bg-light rounded-xl border border-border-gray p-6 md:p-8">
                <h3 className="text-xl font-bold text-primary mb-2">
                    Fiscal Year {taxYear || 2026}
                </h3>
                <p className="text-sm text-gray mb-6">{totalDays}/365 Days</p>

                {/* Progress Bar */}
                <div className="mb-6">
                    <div className="relative w-full h-3 bg-border-gray rounded-full overflow-hidden">
                        <div
                            className="absolute h-full bg-primary transition-all duration-300"
                            style={{ width: `${progressPercentage}%` }}
                        />
                    </div>
                    <div className="flex justify-between text-xs text-gray mt-2">
                        <span>Jan 1</span>
                        <span>Dec 31</span>
                    </div>
                </div>

                {/* Status Message */}
                {isYearComplete && (
                    <div className="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start gap-3">
                        <CheckCircle2 className="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" />
                        <p className="text-sm text-green-700">
                            Great! You have accounted for all 365 days of the
                            year.
                        </p>
                    </div>
                )}

                {!isYearComplete && daysRemaining > 0 && (
                    <div className="bg-primary bg-opacity-5 border border-primary border-opacity-20 rounded-lg p-4 flex items-start gap-3">
                        <TriangleAlert className="w-5 h-5 text-primary flex-shrink-0 mt-0.5" />
                        <p className="text-sm text-primary">
                            You have <strong>{daysRemaining} days</strong>{" "}
                            remaining to account for in {taxYear || 2026}.
                        </p>
                    </div>
                )}

                {hasErrors && (
                    <div className="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3">
                        <TriangleAlert className="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                        <p className="text-sm text-red-700">
                            Total days exceed 365. Please adjust your entries.
                        </p>
                    </div>
                )}
            </div>

            {/* Add Residency Period */}
            <div className="bg-light rounded-xl border border-border-gray p-6 md:p-8">
                <h3 className="text-lg font-bold text-primary mb-6 uppercase text-sm tracking-wide">
                    Add Residency Period
                </h3>

                <div className="grid grid-cols-1 xl:grid-cols-12 gap-4 mb-4">
                    {/* Country Search */}
                    <div
                        className={
                            selectedCountry?.code === "US"
                                ? "xl:col-span-5"
                                : "xl:col-span-8"
                        }
                    >
                        <Select
                            placeholder="Search country..."
                            value={selectedCountry?.id?.toString() || ""}
                            onChange={(val) => {
                                const country = countries.find(
                                    (c) => c.id.toString() === val,
                                );
                                setSelectedCountry(country || null);
                                setSelectedStateId("");
                            }}
                            options={countryOptions}
                        />
                    </div>

                    {/* State Search (Only for US) */}
                    {selectedCountry?.code === "US" && (
                        <div className="xl:col-span-4">
                            <Select
                                placeholder="Select State..."
                                value={selectedStateId}
                                onChange={(val) => setSelectedStateId(val)}
                                options={states.map((s) => ({
                                    value: s.id.toString(),
                                    label: s.name,
                                }))}
                            />
                        </div>
                    )}

                    {/* Days Spent */}
                    <div
                        className={
                            selectedCountry?.code === "US"
                                ? "xl:col-span-3"
                                : "xl:col-span-4"
                        }
                    >
                        <input
                            type="number"
                            placeholder="Days spent"
                            value={daysSpent}
                            onChange={(e) => setDaysSpent(e.target.value)}
                            onKeyDown={(e) => {
                                if (e.key === "Enter") {
                                    e.preventDefault();
                                    handleAddPeriod();
                                }
                            }}
                            min="1"
                            max={daysRemaining}
                            className={`w-full px-4 py-3 border rounded-lg text-base font-sans focus:ring-2 focus:ring-opacity-50 focus:border-transparent outline-none transition ${
                                isOverLimit
                                    ? "border-red-500 focus:ring-red-500 text-red-600"
                                    : "border-border-gray focus:ring-primary"
                            }`}
                        />
                        {isOverLimit && (
                            <p className="text-xs text-red-500 mt-1 font-medium">
                                You cannot add more than {daysRemaining} day
                                {daysRemaining !== 1 ? "s" : ""}.
                            </p>
                        )}
                    </div>
                </div>

                {/* Add Button */}
                <button
                    type="button"
                    onClick={handleAddPeriod}
                    disabled={
                        !selectedCountry ||
                        (selectedCountry?.code === "US" && !selectedStateId) ||
                        !daysSpent ||
                        parseInt(daysSpent) <= 0 ||
                        isOverLimit
                    }
                    className="px-6 py-3 bg-light border-2 border-primary text-primary font-bold rounded-lg hover:bg-primary hover:text-light disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center gap-2 justify-center"
                >
                    <Plus className="w-5 h-5" />
                    Add
                </button>
            </div>

            {/* Residency Periods List */}
            {residencyPeriods.length > 0 && (
                <div className="space-y-3">
                    {residencyPeriods.map((period) => (
                        <ResidencyPeriodItem
                            key={period.id}
                            country={
                                period.state_name
                                    ? `${period.country_name} (${period.state_name})`
                                    : period.country_name
                            }
                            country_name={period.country_name}
                            country_code={period.country_code}
                            country_tax_basis={period.country_tax_basis}
                            days={period.days}
                            localIncome={period.local_income}
                            localIncomeCurrency={
                                period.local_income_currency ?? step1Currency
                            }
                            step1Currency={step1Currency}
                            dateRange={`Jan 1 - Dec 31`}
                            isTaxResident={period.isTaxResident}
                            selectedTaxTypes={period.selected_tax_types}
                            availableTaxTypes={taxTypes}
                            onRemove={() => handleRemovePeriod(period.id)}
                            onUpdate={(field, value) =>
                                handleUpdatePeriod(period.id, field, value)
                            }
                        />
                    ))}
                </div>
            )}

            {/* Navigation Buttons */}
            <div className="flex gap-4 pt-8 border-t border-border-gray">
                <button
                    type="button"
                    onClick={onBack}
                    className="flex-1 border-2 border-primary text-primary font-bold py-4 px-6 rounded-lg hover:bg-primary hover:text-light transition-all flex items-center justify-center gap-2"
                >
                    <ChevronLeft className="w-5 h-5" />
                    Back
                </button>
                <button
                    type="submit"
                    disabled={isSubmitting || processing || !isYearComplete}
                    className="flex-1 bg-primary hover:bg-dark disabled:bg-gray disabled:cursor-not-allowed text-light font-bold py-4 px-6 rounded-lg transition-all flex items-center justify-center gap-2"
                >
                    {isSubmitting || processing ? (
                        <>
                            <Loader />
                            Processing...
                        </>
                    ) : (
                        <>
                            Calculate Liability
                            <ArrowRight className="w-5 h-5" />
                        </>
                    )}
                </button>
            </div>
        </form>
    );
}
