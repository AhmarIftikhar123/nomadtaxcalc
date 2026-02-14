"use client";

import React from "react";
import { DollarSign, Globe, Briefcase } from "lucide-react";

export default function Form1Summary({ formData }) {
    const formatCurrency = (value, currency) => {
        try {
            const formatter = new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: currency,
                minimumFractionDigits: 0,
            });
            return formatter.format(value);
        } catch {
            return `${currency} ${Number(value).toLocaleString()}`;
        }
    };

    // Use ISO code directly from backend (no manual lookup needed)
    const countryCode = (
        formData.citizenship_country_code || "US"
    ).toUpperCase();
    const countryName =
        formData.citizenship_country_name ||
        formData.country_of_citizenship ||
        "Unknown";

    return (
        <div className="bg-white rounded-xl border border-border-gray p-6 md:p-8 shadow-sm mb-8">
            <h3 className="text-lg font-bold text-primary mb-6 flex items-center gap-2">
                <Briefcase className="w-5 h-5" />
                Your Information from Step 1
            </h3>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {/* Annual Gross Income */}
                <div className="flex items-start gap-4">
                    <div className="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center">
                        <DollarSign className="w-6 h-6 text-primary" />
                    </div>
                    <div>
                        <p className="text-sm text-gray font-medium mb-1">
                            Annual Gross Income
                        </p>
                        <p className="text-lg font-bold text-primary">
                            {formatCurrency(
                                formData.annual_income,
                                formData.currency,
                            )}
                        </p>
                    </div>
                </div>

                {/* Currency */}
                <div className="flex items-start gap-4">
                    <div className="w-12 h-12 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center">
                        <span className="text-primary font-bold">
                            {formData.currency}
                        </span>
                    </div>
                    <div>
                        <p className="text-sm text-gray font-medium mb-1">
                            Currency
                        </p>
                        <p className="text-lg font-bold text-primary">
                            {formData.currency}
                        </p>
                    </div>
                </div>

                {/* Country of Citizenship */}
                <div className="flex items-start gap-4">
                    <img
                        src={`https://flagcdn.com/w80/${countryCode.toLowerCase()}.png`}
                        alt={countryName}
                        className="w-12 h-12 rounded-lg object-cover"
                        onError={(e) => {
                            e.target.style.display = "none";
                        }}
                    />
                    <div>
                        <p className="text-sm text-gray font-medium mb-1">
                            Country of Citizenship
                        </p>
                        <p className="text-lg font-bold text-primary">
                            {countryName}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
}
