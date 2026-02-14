"use client";

import React, { useState } from "react";
import { AlertTriangle, MapPin, ChevronRight, ShieldCheck } from "lucide-react";

export default function ResidencyRiskAlert({ residencyData }) {
    const [showDetails, setShowDetails] = useState(false);

    // residencyData is now an array of countries from the backend
    if (
        !residencyData ||
        !Array.isArray(residencyData) ||
        residencyData.length === 0
    ) {
        return null;
    }

    // Filter: only show alerts for countries WITH income tax AND enough days
    const alertCountries = residencyData.filter(
        (r) =>
            r.has_income_tax && r.days_spent >= Math.max(r.threshold - 30, 120),
    );

    // Also find zero-tax countries where user spent significant time (good news!)
    const zeroTaxCountries = residencyData.filter(
        (r) => !r.has_income_tax && r.days_spent >= 150,
    );

    if (alertCountries.length === 0 && zeroTaxCountries.length === 0) {
        return null;
    }

    return (
        <div className="mb-12 space-y-4">
            {/* Zero-tax country positive message */}
            {zeroTaxCountries.map((country) => (
                <div
                    key={country.country_id}
                    className="rounded-xl p-6 md:p-8 border-l-4 bg-green-50 border-green-500"
                >
                    <div className="flex items-start gap-4">
                        <div className="flex-shrink-0">
                            <ShieldCheck className="w-6 h-6 text-green-600" />
                        </div>
                        <div className="flex-1">
                            <h3 className="text-lg font-bold mb-2 text-green-700">
                                No Income Tax in {country.country_name}
                            </h3>
                            <p className="text-sm leading-relaxed text-green-700">
                                You spent{" "}
                                <strong>{country.days_spent} days</strong> in{" "}
                                {country.country_name}, which has{" "}
                                <strong>no personal income tax</strong>. Time
                                spent here does not generate additional tax
                                liability.
                            </p>
                        </div>
                    </div>
                </div>
            ))}

            {/* Risk alerts for taxable countries */}
            {alertCountries.map((country) => {
                const riskLevel =
                    country.days_spent >= country.threshold
                        ? "high"
                        : "warning";
                const daysRemaining = country.threshold - country.days_spent;

                return (
                    <div
                        key={country.country_id}
                        className={`rounded-xl p-6 md:p-8 border-l-4 ${
                            riskLevel === "high"
                                ? "bg-red-50 border-red-500"
                                : "bg-yellow-50 border-yellow-500"
                        }`}
                    >
                        <div className="flex items-start gap-4">
                            <div className="flex-shrink-0">
                                <AlertTriangle
                                    className={`w-6 h-6 ${
                                        riskLevel === "high"
                                            ? "text-red-600"
                                            : "text-yellow-600"
                                    }`}
                                />
                            </div>
                            <div className="flex-1">
                                <h3
                                    className={`text-lg font-bold mb-2 ${
                                        riskLevel === "high"
                                            ? "text-red-700"
                                            : "text-yellow-800"
                                    }`}
                                >
                                    {riskLevel === "high"
                                        ? `Residency Risk Alert — ${country.country_name}`
                                        : `Residency Warning — ${country.country_name}`}
                                </h3>
                                <p
                                    className={`text-sm leading-relaxed ${
                                        riskLevel === "high"
                                            ? "text-red-700"
                                            : "text-yellow-700"
                                    }`}
                                >
                                    You have spent{" "}
                                    <strong>{country.days_spent} days</strong>{" "}
                                    in {country.country_name}. You are{" "}
                                    {riskLevel === "high"
                                        ? `exceeding the ${country.threshold}-day tax residency threshold`
                                        : `approaching the ${country.threshold}-day tax residency threshold (${Math.abs(daysRemaining)} days remaining)`}
                                    , which may trigger full tax liability on
                                    worldwide income.
                                </p>
                            </div>
                        </div>
                    </div>
                );
            })}

            {/* Expandable details */}
            {alertCountries.length > 0 && (
                <>
                    <button
                        onClick={() => setShowDetails(!showDetails)}
                        className="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-colors bg-red-100 text-red-700 hover:bg-red-200"
                    >
                        {showDetails ? "Hide" : "View"} Risk Details
                        <ChevronRight
                            className={`w-4 h-4 transition-transform ${
                                showDetails ? "rotate-90" : ""
                            }`}
                        />
                    </button>

                    {showDetails && (
                        <div className="bg-white rounded-xl border border-border-gray p-6 md:p-8">
                            <h4 className="text-lg font-bold text-primary mb-4">
                                Risk Details
                            </h4>
                            <div className="space-y-4">
                                <div className="flex items-start gap-3">
                                    <MapPin className="w-5 h-5 text-primary flex-shrink-0 mt-0.5" />
                                    <div>
                                        <p className="font-semibold text-primary">
                                            Residency Threshold
                                        </p>
                                        <p className="text-sm text-gray">
                                            Each country has its own threshold
                                            (commonly 183 days but varies).
                                            Exceeding it triggers tax residency.
                                        </p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <AlertTriangle className="w-5 h-5 text-primary flex-shrink-0 mt-0.5" />
                                    <div>
                                        <p className="font-semibold text-primary">
                                            Worldwide Income Liability
                                        </p>
                                        <p className="text-sm text-gray">
                                            Tax residents are typically liable
                                            to pay taxes on all worldwide
                                            income, not just income earned in
                                            that country.
                                        </p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <MapPin className="w-5 h-5 text-primary flex-shrink-0 mt-0.5" />
                                    <div>
                                        <p className="font-semibold text-primary">
                                            Actions to Consider
                                        </p>
                                        <ul className="text-sm text-gray list-disc list-inside mt-1">
                                            <li>
                                                Optimize residency in lower-tax
                                                jurisdictions
                                            </li>
                                            <li>
                                                Consult with a tax professional
                                            </li>
                                            <li>
                                                Review treaty benefits if
                                                applicable
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </>
            )}
        </div>
    );
}
