"use client";

import React from "react";
import { Lock, ArrowRight, Loader } from "lucide-react";
import InputError from "@/Components/InputError";
import Select from "@/Components/Form/Select";

export default function Step1Form({
    data,
    setData,
    errors,
    countries,
    currencies,
    processing,
}) {
    // Format options for Select component
    const currencyOptions = currencies.map((curr) => ({
        value: curr.code,
        label: curr.name,
    }));

    const countryOptions = countries.map((country) => ({
        value: country.id,
        label: country.name,
    }));

    return (
        <div className="space-y-8">
            {/* Annual Gross Income */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
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
            </div>

            {/* Country of Citizenship */}
            <div>
                <Select
                    label="Country of Citizenship"
                    value={data.citizenship_country_id}
                    onChange={(value) =>
                        setData("citizenship_country_id", value)
                    }
                    options={countryOptions}
                    error={errors.citizenship_country_id}
                    placeholder="Select your country"
                    helpText="Your primary country of residence or citizenship for tax purposes"
                />
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
