"use client";

import React from "react";
import { FileText, CheckCircle2 } from "lucide-react";

export default function TreatiesApplied({ treatiesApplied, currency }) {
    if (!treatiesApplied || treatiesApplied.length === 0) {
        return (
            <div className="bg-white rounded-xl border border-border-gray p-6 mb-4 shadow-sm">
                <div className="flex items-center gap-3">
                    <div className="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center">
                        <FileText className="w-5 h-5 text-gray-400" />
                    </div>
                    <div>
                        <h3 className="text-lg font-bold text-primary">
                            No Treaties Applied
                        </h3>
                        <p className="text-sm text-gray">
                            No international tax treaties were applicable to
                            your current residency profile.
                        </p>
                    </div>
                </div>
            </div>
        );
    }

    const formatCurrency = (value) => {
        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: currency || "USD",
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    };

    return (
        <div className="bg-white rounded-xl border border-border-gray p-6 mb-4 shadow-sm">
            <div className="flex items-center gap-3 mb-4">
                <div className="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <FileText className="w-5 h-5 text-green-600" />
                </div>
                <div>
                    <h3 className="text-lg font-bold text-primary">
                        Treaties Applied
                    </h3>
                    <p className="text-sm text-gray">
                        International tax treaties were automatically applied to
                        reduce your double taxation.
                    </p>
                </div>
            </div>

            <div className="space-y-3 mt-4">
                {treatiesApplied.map((treaty, idx) => (
                    <div
                        key={idx}
                        className="flex flex-col md:flex-row md:items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg gap-4"
                    >
                        <div className="flex items-start gap-3">
                            <CheckCircle2 className="w-5 h-5 text-green-600 mt-0.5 shrink-0" />
                            <div>
                                <p className="font-bold text-green-900">
                                    {treaty.countries.join("-")} Tax Treaty (
                                    {treaty.type === "credit"
                                        ? "Foreign Tax Credit"
                                        : "Exemption"}
                                    )
                                </p>
                                <p className="text-sm text-green-700">
                                    This agreement successfully mitigated double
                                    taxation on your global income.
                                </p>
                            </div>
                        </div>
                        <div className="text-left md:text-right shrink-0">
                            <p className="text-sm text-green-700 font-medium">
                                Estimated Savings
                            </p>
                            <p className="text-xl font-bold text-green-700">
                                {formatCurrency(treaty.tax_saved)}
                            </p>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}
