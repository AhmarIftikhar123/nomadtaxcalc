"use client";

import React, { useMemo } from "react";
import { Lock, ArrowRight, Loader, Info } from "lucide-react";
import InputError from "@/Components/InputError";
import Select from "@/Components/Form/Select";

export default function Step1Form({
    data,
    setData,
    errors,
    countries,
    states = [],
    currencies,
    availableYears,
    processing,
}) {
    const selectedCountry = countries.find(
        (c) => c.id === Number(data.citizenship_country_id),
    );
    const isUSCitizen = selectedCountry?.code === "US";

    // Format options for Select component
    const currencyOptions = currencies.map((curr) => ({
        value: curr.code,
        label: curr.name,
    }));

    const countryOptions = countries.map((country) => ({
        value: country.id,
        label: country.name,
    }));

    const stateOptions = states.map((state) => ({
        value: state.id,
        label: state.name,
    }));

    const yearOptions = (availableYears || []).map((year) => ({
        value: String(year),
        label: `${year}`,
    }));

    // Dynamic planning message based on selected tax year
    const currentCalendarYear = new Date().getFullYear();
    const selectedYear = Number(data.tax_year);

    const yearPlanningMessage = useMemo(() => {
        if (!selectedYear) return null;

        if (selectedYear > currentCalendarYear) {
            return `You're planning ahead — tax brackets for ${selectedYear} will be used for future income projections.`;
        }
        if (selectedYear === currentCalendarYear) {
            return `You're estimating taxes for an active year (${selectedYear}). Brackets and treaty data are current as of today but may update before year-end.`;
        }
        // Past year
        return `Filing for tax year ${selectedYear} — calculations use the ${selectedYear} tax brackets and treaties.`;
    }, [selectedYear, currentCalendarYear]);

    return (
        <div className="space-y-8">
            {/* Annual Gross Income */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <label
                        htmlFor="annual_income"
                        className="block text-sm font-semibold text-primary mb-2"
                    >
                        Annual Gross Income
                    </label>
                    <input
                        id="annual_income"
                        type="number"
                        placeholder="e.g. 85,000"
                        value={data.annual_income}
                        onChange={(e) =>
                            setData("annual_income", e.target.value)
                        }
                        className={`w-full px-4 py-3 border rounded-lg text-base font-sans focus:ring-2 focus:ring-primary focus:ring-opacity-50 focus:border-transparent outline-none transition ${
                            errors.annual_income
                                ? "border-red-500"
                                : "border-border-gray"
                        }`}
                    />
                    <InputError
                        message={errors.annual_income}
                        className="mt-2"
                    />
                </div>

                {/* Currency */}
                <div>
                    <Select
                        label="Currency"
                        value={data.currency}
                        onChange={(value) => setData("currency", value)}
                        options={currencyOptions}
                        error={errors.currency}
                        placeholder="Select currency"
                    />
                </div>

                {/* Tax Year */}
                <div>
                    <Select
                        label="Tax Year"
                        value={String(data.tax_year)}
                        onChange={(value) =>
                            setData("tax_year", value ? Number(value) : "")
                        }
                        options={yearOptions}
                        error={errors.tax_year}
                        placeholder="Select year"
                    />
                </div>
            </div>

            {/* Tax Year Planning Message */}
            {yearPlanningMessage && (
                <div className="flex items-start gap-3 rounded-lg bg-blue-50 border border-blue-200 px-4 py-3 -mt-4">
                    <Info className="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                    <p className="text-sm text-blue-800 leading-relaxed">
                        {yearPlanningMessage}
                    </p>
                </div>
            )}

            {/* Country of Citizenship */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <Select
                        label="Country of Citizenship"
                        value={data.citizenship_country_id}
                        onChange={(value) => {
                            setData((prev) => {
                                const country = countries.find(
                                    (c) => c.id === Number(value),
                                );
                                return {
                                    ...prev,
                                    citizenship_country_id: value,
                                    ...(country?.code !== "US"
                                        ? { domicile_state_id: "" }
                                        : {}),
                                };
                            });
                        }}
                        options={countryOptions}
                        error={errors.citizenship_country_id}
                        placeholder="Select your country"
                        helpText="Your primary country of residence or citizenship for tax purposes"
                    />
                </div>

                {/* State of Domicile (Only visible for US citizens) */}
                {isUSCitizen && (
                    <div>
                        <Select
                            label="State of Domicile"
                            value={data.domicile_state_id}
                            onChange={(value) =>
                                setData("domicile_state_id", value)
                            }
                            options={stateOptions}
                            error={errors.domicile_state_id}
                            placeholder="Select state"
                            helpText="Required for state tax calculations"
                        />
                    </div>
                )}
            </div>

            {/* Security Notice */}
            <div className="bg-primary items-center bg-opacity-5 border border-primary border-opacity-20 rounded-lg p-4 md:p-6 flex gap-4">
                <Lock className="w-6 h-6 text-primary flex-shrink-0 mt-0.5" />
                <p className="text-sm text-primary">
                    Your data is encrypted and only used for this calculation.
                    We do not store your personal financial details without your
                    permission.
                </p>
            </div>

            {/* Submit Button */}
            <button
                type="submit"
                disabled={processing}
                className="w-full bg-primary hover:bg-dark disabled:bg-gray text-light font-bold py-4 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2"
            >
                {processing ? (
                    <>
                        <Loader className="w-5 h-5 animate-spin" />
                        Processing...
                    </>
                ) : (
                    <>
                        Continue to Step 2
                        <ArrowRight className="w-5 h-5" />
                    </>
                )}
            </button>
        </div>
    );
}
