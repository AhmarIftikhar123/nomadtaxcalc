"use client";

import React from "react";
import ResidencyRiskAlert from "@/Components/TaxCalculator/ResidencyRiskAlert";

/**
 * ResidencyRiskComparison — side-by-side residency risk panels reusing the existing component.
 *
 * Props:
 *   resultA : { residency_data: [...] }
 *   resultB : { residency_data: [...] }
 */
export default function ResidencyRiskComparison({ resultA, resultB }) {
    const dataA = resultA?.residency_data || [];
    const dataB = resultB?.residency_data || [];

    // Determine if either has active risks
    const hasRisksA = dataA.some((r) => r.is_tax_resident || r.near_threshold);
    const hasRisksB = dataB.some((r) => r.is_tax_resident || r.near_threshold);

    return (
        <div className="space-y-4">
            <h3 className="text-sm font-bold text-primary uppercase tracking-wider">
                Residency Risk
            </h3>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {/* Scenario A */}
                <div
                    className={`rounded-xl border ${hasRisksA ? "border-red-200 bg-red-50/50" : "border-green-200 bg-green-50/50"} p-1`}
                >
                    <div
                        className={`text-xs font-extrabold uppercase tracking-wider px-4 py-2 rounded-t-lg ${hasRisksA ? "text-red-600" : "text-green-700"}`}
                    >
                        Scenario A —{" "}
                        {hasRisksA ? "Active Risks" : "Clean Profile"}
                    </div>
                    <div className="p-2">
                        {dataA.length > 0 ? (
                            <ResidencyRiskAlert residencyData={dataA} />
                        ) : (
                            <p className="text-sm text-gray p-3">
                                No residency data
                            </p>
                        )}
                    </div>
                </div>

                {/* Scenario B */}
                <div
                    className={`rounded-xl border ${hasRisksB ? "border-red-200 bg-red-50/50" : "border-green-200 bg-green-50/50"} p-1`}
                >
                    <div
                        className={`text-xs font-extrabold uppercase tracking-wider px-4 py-2 rounded-t-lg ${hasRisksB ? "text-red-600" : "text-green-700"}`}
                    >
                        Scenario B —{" "}
                        {hasRisksB ? "Active Risks" : "Clean Profile"}
                    </div>
                    <div className="p-2">
                        {dataB.length > 0 ? (
                            <ResidencyRiskAlert residencyData={dataB} />
                        ) : (
                            <p className="text-sm text-gray p-3">
                                No residency data
                            </p>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
