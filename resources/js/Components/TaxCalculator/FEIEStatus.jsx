"use client";

import React from "react";
import { Flag, CheckCircle2, AlertTriangle, Info } from "lucide-react";

export default function FEIEStatus({
    feieResult,
    citizenshipCountryCode,
    currency,
    taxYear,
}) {
    if (citizenshipCountryCode !== "US") {
        return (
            <div className="bg-white rounded-xl border border-border-gray p-6 mb-4 shadow-sm">
                <div className="flex items-center gap-3">
                    <div className="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center">
                        <Info className="w-5 h-5 text-gray-400" />
                    </div>
                    <div>
                        <h3 className="text-lg font-bold text-primary">
                            FEIE Not Applicable
                        </h3>
                        <p className="text-sm text-gray">
                            The Foreign Earned Income Exclusion is only
                            applicable to US Citizens.
                        </p>
                    </div>
                </div>
            </div>
        );
    }

    // Still load even if no result was fully processed, but we might encounter null.
    // So safe fallback for missing feieResult object
    if (!feieResult) {
        return null;
    }

    const {
        eligible,
        days_outside_us,
        minimum_required,
        feie_limit,
        excluded_income,
        taxable_us_income,
        reason,
    } = feieResult;

    const formatCurrency = (value) => {
        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: currency || "USD",
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    };

    if (eligible) {
        return (
            <div className="bg-green-50 border border-green-200 rounded-xl p-6 mb-4 shadow-sm">
                <div className="flex items-center gap-3 mb-4">
                    <div className="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
                        <Flag className="w-5 h-5 text-green-600" />
                    </div>
                    <div>
                        <h3 className="text-lg font-bold text-green-900 leading-tight">
                            Foreign Earned Income Exclusion (FEIE)
                        </h3>
                        <p className="text-sm text-green-700 mt-1">
                            You meet the Physical Presence Test to exclude
                            foreign income.
                        </p>
                    </div>
                    <div className="ml-auto flex items-center gap-1.5 bg-green-600 text-white px-3 py-1 rounded-full text-sm font-bold shadow-sm">
                        <CheckCircle2 className="w-4 h-4" />
                        Eligible
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div className="bg-white rounded-lg p-4 border border-green-100">
                        <p className="text-xs text-green-800 uppercase tracking-wide font-bold mb-1">
                            Days Outside US
                        </p>
                        <p className="text-2xl font-bold text-green-700">
                            {days_outside_us}{" "}
                            <span className="text-sm font-medium opacity-70">
                                / {minimum_required} req.
                            </span>
                        </p>
                    </div>
                    <div className="bg-white rounded-lg p-4 border border-green-100">
                        <p className="text-xs text-green-800 uppercase tracking-wide font-bold mb-1">
                            Excluded Income
                        </p>
                        <p className="text-2xl font-bold text-green-700">
                            {formatCurrency(excluded_income)}
                        </p>
                        {excluded_income === feie_limit && (
                            <p className="text-xs text-green-600 mt-1">
                                Capped at {taxYear || 2026} limit
                            </p>
                        )}
                    </div>
                    <div className="bg-white rounded-lg p-4 border border-green-100">
                        <p className="text-xs text-green-800 uppercase tracking-wide font-bold mb-1">
                            Taxable US Income
                        </p>
                        <p className="text-2xl font-bold text-primary">
                            {formatCurrency(taxable_us_income)}
                        </p>
                    </div>
                </div>
            </div>
        );
    }

    // If not eligible
    return (
        <div className="bg-orange-50 border border-orange-200 rounded-xl p-6 mb-4 shadow-sm">
            <div className="flex items-center gap-3 mb-4">
                <div className="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center shrink-0">
                    <AlertTriangle className="w-5 h-5 text-orange-600" />
                </div>
                <div>
                    <h3 className="text-lg font-bold text-orange-900 leading-tight">
                        Foreign Earned Income Exclusion (FEIE)
                    </h3>
                    <p className="text-sm text-orange-800 mt-1">
                        You do not meet the Physical Presence Test requirements.
                    </p>
                </div>
            </div>

            <div className="mt-4 bg-white/60 p-4 rounded-lg border border-orange-100 flex items-center justify-between">
                <div>
                    <p className="text-sm text-orange-800 font-medium">
                        Days Spent Outside US
                    </p>
                    <p className="text-2xl font-bold text-orange-700">
                        {days_outside_us}{" "}
                        <span className="text-sm font-medium">days</span>
                    </p>
                </div>
                <div className="text-right">
                    <p className="text-sm text-orange-800 font-medium">
                        Required for FEIE
                    </p>
                    <p className="text-2xl font-bold text-primary">
                        330 <span className="text-sm font-medium">days</span>
                    </p>
                </div>
            </div>

            <p className="text-sm text-orange-800 mt-3 font-medium">{reason}</p>
        </div>
    );
}
