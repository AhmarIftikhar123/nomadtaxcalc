"use client";

import React, { useState } from "react";
import { AlertTriangle, MapPin, ChevronRight, ShieldCheck } from "lucide-react";
export default function ResidencyRiskAlert({ residencyData }) {
    const [showDetails, setShowDetails] = useState(false);

    if (!residencyData || !Array.isArray(residencyData) || residencyData.length === 0) {
        return null;
    }

    // Separate into distinct cases using the backend's is_citizenship_based flag.
    // US citizens are always tax-resident regardless of days — they must NOT
    // appear in the day-threshold sections (confirmedResidents, nearThreshold, etc.)
    const citizenshipBased = residencyData.filter(r => r.is_citizenship_based && r.has_income_tax);
    const confirmedResidents = residencyData.filter(r =>
        r.is_tax_resident &&
        !r.is_citizenship_based &&
        r.has_income_tax &&
        r.threshold > 0
    );
    const nearThreshold = residencyData.filter(r =>
        !r.is_tax_resident &&
        !r.is_citizenship_based &&
        r.has_income_tax &&
        r.threshold > 0 &&
        (r.threshold - (r.adjusted_days ?? r.days_spent)) <= 30
    );
    const safeNonResidents = residencyData.filter(r =>
        !r.is_tax_resident &&
        !r.is_citizenship_based &&
        r.has_income_tax &&
        r.threshold > 0 &&
        (r.threshold - (r.adjusted_days ?? r.days_spent)) > 30
    );
    const zeroTaxCountries = residencyData.filter(r => !r.has_income_tax && !r.is_citizenship_based && r.days_spent >= 150);

    return (
        <div className="mb-12 space-y-4">

            {/* Citizenship-based taxation */}
            {citizenshipBased.map(country => (
                <div key={country.country_id} className="rounded-xl p-6 md:p-8 border-l-4 bg-red-50 border-red-500">
                    <div className="flex items-start gap-4">
                        <AlertTriangle className="w-6 h-6 text-red-600 flex-shrink-0" />
                        <div>
                            <h3 className="text-lg font-bold text-red-700 mb-2">
                                Citizenship-Based Taxation — {country.country_name}
                            </h3>
                            <p className="text-sm text-red-700">
                                As a citizen of {country.country_name}, you owe tax on 
                                worldwide income regardless of where you live.
                            </p>
                        </div>
                    </div>
                </div>
            ))}

            {/* Confirmed tax residents — NOT "may trigger", it already triggered */}
            {confirmedResidents.map(country => {
                const days = country.adjusted_days ?? country.days_spent;
                const daysOver = days - country.threshold;
                const isBarelyResident = daysOver <= 14;
                return (
                    <div key={country.country_id} className="rounded-xl p-6 md:p-8 border-l-4 bg-red-50 border-red-500">
                        <div className="flex items-start gap-4">
                            <AlertTriangle className="w-6 h-6 text-red-600 flex-shrink-0" />
                            <div>
                                <h3 className="text-lg font-bold text-red-700 mb-2">
                                    {isBarelyResident 
                                        ? `Barely Tax Resident — ${country.country_name}`
                                        : `Confirmed Tax Resident — ${country.country_name}`
                                    }
                                </h3>
                                <p className="text-sm text-red-700">
                                    You spent <strong>{country.days_spent} days</strong> in {country.country_name}, 
                                    exceeding the {country.threshold}-day threshold by <strong>{daysOver} days</strong>. 
                                    {isBarelyResident 
                                        ? " Minor travel adjustments next year could avoid this."
                                        : " You are liable for tax on worldwide income in this country."
                                    }
                                </p>
                            </div>
                        </div>
                    </div>
                );
            })}

            {/* Approaching threshold — warning */}
            {nearThreshold.map(country => {
                const daysRemaining = country.threshold - country.days_spent;
                return (
                    <div key={country.country_id} className="rounded-xl p-6 md:p-8 border-l-4 bg-yellow-50 border-yellow-500">
                        <div className="flex items-start gap-4">
                            <AlertTriangle className="w-6 h-6 text-yellow-600 flex-shrink-0" />
                            <div>
                                <h3 className="text-lg font-bold text-yellow-800 mb-2">
                                    Residency Warning — {country.country_name}
                                </h3>
                                <p className="text-sm text-yellow-700">
                                    You spent <strong>{country.days_spent} days</strong> in {country.country_name}. 
                                    Only <strong>{daysRemaining} days remaining</strong> before you trigger 
                                    the {country.threshold}-day tax residency threshold.
                                </p>
                            </div>
                        </div>
                    </div>
                );
            })}

            {/* Safe non-residents — neutral informational */}
            {safeNonResidents.map(country => {
                const daysRemaining = country.threshold - country.days_spent;
                return (
                    <div key={country.country_id} className="rounded-xl p-6 md:p-8 border-l-4 bg-gray-50 border-gray-300">
                        <div className="flex items-start gap-4">
                            <ShieldCheck className="w-6 h-6 text-gray-500 flex-shrink-0" />
                            <div>
                                <h3 className="text-lg font-bold text-gray-700 mb-2">
                                    Non-Resident — {country.country_name}
                                </h3>
                                <p className="text-sm text-gray-600">
                                    You spent <strong>{country.days_spent} days</strong> in {country.country_name} — 
                                    well below the {country.threshold}-day threshold 
                                    (<strong>{daysRemaining} days remaining</strong>). No tax residency triggered.
                                </p>
                            </div>
                        </div>
                    </div>
                );
            })}

            {/* Zero-tax positive message */}
            {zeroTaxCountries.map(country => (
                <div key={country.country_id} className="rounded-xl p-6 md:p-8 border-l-4 bg-green-50 border-green-500">
                    <div className="flex items-start gap-4">
                        <ShieldCheck className="w-6 h-6 text-green-600 flex-shrink-0" />
                        <div>
                            <h3 className="text-lg font-bold text-green-700 mb-2">
                                No Income Tax — {country.country_name}
                            </h3>
                            <p className="text-sm text-green-700">
                                You spent <strong>{country.days_spent} days</strong> in {country.country_name}, 
                                which has no personal income tax. No additional liability here.
                            </p>
                        </div>
                    </div>
                </div>
            ))}

            {/* Risk details expandable — only if any confirmed residents or near threshold */}
            {(confirmedResidents.length > 0 || nearThreshold.length > 0 || citizenshipBased.length > 0) && (
                <>
                    <button
                        onClick={() => setShowDetails(!showDetails)}
                        className="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-colors bg-red-100 text-red-700 hover:bg-red-200"
                    >
                        {showDetails ? "Hide" : "View"} Risk Details
                        <ChevronRight className={`w-4 h-4 transition-transform ${showDetails ? "rotate-90" : ""}`} />
                    </button>

                    {showDetails && (
                        <div className="bg-white rounded-xl border border-border-gray p-6 md:p-8">
                            <h4 className="text-lg font-bold text-primary mb-4">Risk Details</h4>
                            <div className="space-y-4">
                                <div className="flex items-start gap-3">
                                    <MapPin className="w-5 h-5 text-primary flex-shrink-0 mt-0.5" />
                                    <div>
                                        <p className="font-semibold text-primary">Residency Threshold</p>
                                        <p className="text-sm text-gray">
                                            Each country has its own threshold (commonly 183 days but varies). 
                                            Exceeding it triggers tax residency.
                                        </p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <AlertTriangle className="w-5 h-5 text-primary flex-shrink-0 mt-0.5" />
                                    <div>
                                        <p className="font-semibold text-primary">Worldwide Income Liability</p>
                                        <p className="text-sm text-gray">
                                            Tax residents are typically liable to pay taxes on all worldwide 
                                            income, not just income earned in that country.
                                        </p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <MapPin className="w-5 h-5 text-primary flex-shrink-0 mt-0.5" />
                                    <div>
                                        <p className="font-semibold text-primary">Actions to Consider</p>
                                        <ul className="text-sm text-gray list-disc list-inside mt-1">
                                            <li>Optimize residency in lower-tax jurisdictions</li>
                                            <li>Consult with a tax professional</li>
                                            <li>Review treaty benefits if applicable</li>
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