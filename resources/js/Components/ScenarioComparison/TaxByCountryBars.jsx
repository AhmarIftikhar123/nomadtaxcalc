"use client";

import React from "react";

/**
 * TaxByCountryBars — horizontal bar chart comparing A vs B tax per country.
 *
 * Props:
 *   perCountry : [{ country_code, country_name, daysA, daysB, residentA, residentB, taxA, taxB }]
 *   currency   : "USD"
 */
export default function TaxByCountryBars({
    perCountry = [],
    currency = "USD",
}) {
    const fmt = (val) =>
        new Intl.NumberFormat("en-US", {
            style: "currency",
            currency,
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(val);

    // Find max tax for scale
    const maxTax = Math.max(...perCountry.flatMap((c) => [c.taxA, c.taxB]), 1);

    const residencyLabel = (days, isResident) => {
        if (days === null || days === undefined) return "Not visited";
        if (isResident) return `Resident ${days}d`;
        return `Non-Resident ${days}d`;
    };

    return (
        <div className="bg-white rounded-xl border border-border-gray p-5 shadow-sm">
            <div className="flex items-center justify-between mb-5">
                <h3 className="text-sm font-bold text-primary">
                    Tax by Country
                </h3>
                <div className="flex items-center gap-4 text-xs">
                    <span className="flex items-center gap-1.5">
                        <span className="w-3 h-3 rounded bg-primary" /> Scenario
                        A
                    </span>
                    <span className="flex items-center gap-1.5">
                        <span className="w-3 h-3 rounded bg-gray" /> Scenario B
                    </span>
                </div>
            </div>

            <div className="space-y-5">
                {perCountry.map((c) => (
                    <div key={c.country_code}>
                        <p className="text-sm font-bold text-primary mb-2">
                            <span className="text-xs text-gray uppercase mr-1.5">
                                {c.country_code}
                            </span>
                            {c.country_name}
                        </p>

                        {/* Bar A */}
                        <div className="flex items-center gap-3 mb-1.5">
                            <div className="flex-1 bg-light rounded-full h-7 overflow-hidden">
                                {c.daysA !== null && c.daysA !== undefined ? (
                                    <div
                                        className="bg-primary h-full rounded-full flex items-center px-3 transition-all duration-500"
                                        style={{
                                            width: `${Math.max((c.taxA / maxTax) * 100, c.taxA > 0 ? 12 : 3)}%`,
                                        }}
                                    >
                                        <span className="text-[11px] font-bold text-light whitespace-nowrap">
                                            {c.taxA > 0
                                                ? `${fmt(c.taxA)} — ${residencyLabel(c.daysA, c.residentA)}`
                                                : ""}
                                        </span>
                                    </div>
                                ) : (
                                    <div className="h-full flex items-center px-3">
                                        <span className="text-[11px] font-medium text-gray">
                                            N/A
                                        </span>
                                    </div>
                                )}
                            </div>
                            <span className="text-[10px] text-gray w-36 text-right flex-shrink-0">
                                A — {residencyLabel(c.daysA, c.residentA)}
                            </span>
                        </div>

                        {/* Bar B */}
                        <div className="flex items-center gap-3">
                            <div className="flex-1 bg-light rounded-full h-7 overflow-hidden">
                                {c.daysB !== null && c.daysB !== undefined ? (
                                    <div
                                        className="bg-gray h-full rounded-full flex items-center px-3 transition-all duration-500"
                                        style={{
                                            width: `${Math.max((c.taxB / maxTax) * 100, c.taxB > 0 ? 12 : 3)}%`,
                                        }}
                                    >
                                        <span className="text-[11px] font-bold text-light whitespace-nowrap">
                                            {c.taxB > 0
                                                ? `${fmt(c.taxB)}`
                                                : "$0"}
                                        </span>
                                    </div>
                                ) : (
                                    <div className="h-full flex items-center px-3">
                                        <span className="text-[11px] font-medium text-gray">
                                            N/A
                                        </span>
                                    </div>
                                )}
                            </div>
                            <span className="text-[10px] text-gray w-36 text-right flex-shrink-0">
                                B — {residencyLabel(c.daysB, c.residentB)}
                            </span>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}
