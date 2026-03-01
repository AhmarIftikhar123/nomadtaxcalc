"use client";

import React from "react";
import Tooltip from "@/Components/Ui/Tooltip";
import { DollarSign, Percent, TrendingDown, TrendingUp } from "lucide-react";

/**
 * ComparisonMetrics — 3-card row: Total Tax, Eff. Rate, Net Income.
 *
 * Props:
 *   resultA  : full result object
 *   resultB  : full result object
 *   diff     : { taxDelta, rateDelta, incomeDelta }
 *   currency : "USD"
 */
export default function ComparisonMetrics({
    resultA,
    resultB,
    diff,
    currency = "USD",
}) {
    const fmt = (val) =>
        new Intl.NumberFormat("en-US", {
            style: "currency",
            currency,
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(val);

    const metrics = [
        {
            label: "Total Tax Liability",
            icon: DollarSign,
            valueA: fmt(resultA?.total_tax ?? 0),
            valueB: fmt(resultB?.total_tax ?? 0),
            delta: fmt(Math.abs(diff?.taxDelta ?? 0)),
            deltaTip:
                diff?.taxDelta > 0
                    ? `${fmt(diff.taxDelta)} less tax in Scenario B`
                    : diff?.taxDelta < 0
                      ? `${fmt(Math.abs(diff.taxDelta))} more tax in Scenario B`
                      : "Same tax in both scenarios",
            positive: diff?.taxDelta > 0, // lower tax in B = good for B
            deltaLabel:
                diff?.taxDelta > 0
                    ? "less tax in B"
                    : diff?.taxDelta < 0
                      ? "more tax in B"
                      : "same",
        },
        {
            label: "Effective Tax Rate",
            icon: Percent,
            valueA: `${resultA?.effective_tax_rate ?? 0}%`,
            valueB: `${resultB?.effective_tax_rate ?? 0}%`,
            delta: `${Math.abs(diff?.rateDelta ?? 0).toFixed(2)}pp`,
            deltaTip:
                diff?.rateDelta > 0
                    ? `${Math.abs(diff.rateDelta).toFixed(2)} percentage points lower in Scenario B`
                    : `${Math.abs(diff?.rateDelta ?? 0).toFixed(2)} percentage points higher in Scenario B`,
            positive: diff?.rateDelta > 0,
            deltaLabel:
                diff?.rateDelta > 0
                    ? "lower in B"
                    : diff?.rateDelta < 0
                      ? "higher in B"
                      : "same",
        },
        {
            label: "Net Income After Tax",
            icon: DollarSign,
            valueA: fmt(resultA?.net_income ?? 0),
            valueB: fmt(resultB?.net_income ?? 0),
            delta: fmt(Math.abs(diff?.incomeDelta ?? 0)),
            deltaTip:
                diff?.incomeDelta > 0
                    ? `${fmt(diff.incomeDelta)} more take-home in Scenario B`
                    : `${fmt(Math.abs(diff?.incomeDelta ?? 0))} less take-home in Scenario B`,
            positive: diff?.incomeDelta > 0,
            deltaLabel:
                diff?.incomeDelta > 0
                    ? "more take-home in B"
                    : diff?.incomeDelta < 0
                      ? "less in B"
                      : "same",
        },
    ];

    return (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {metrics.map((m, i) => {
                const Icon = m.icon;
                const DeltaIcon = m.positive ? TrendingDown : TrendingUp;

                return (
                    <div
                        key={i}
                        className="bg-white rounded-xl border border-border-gray p-5 shadow-sm"
                    >
                        <p className="text-xs text-gray font-semibold uppercase tracking-wider mb-4">
                            {m.label}
                        </p>

                        {/* A vs B values */}
                        <div className="flex items-end gap-4 mb-3">
                            <div>
                                <span className="text-[10px] font-bold uppercase tracking-wider text-primary bg-primary/10 px-1.5 py-0.5 rounded">
                                    A
                                </span>
                                <p className="text-xl font-extrabold text-primary mt-1">
                                    {m.valueA}
                                </p>
                            </div>
                            <div>
                                <span className="text-[10px] font-bold uppercase tracking-wider text-gray bg-light px-1.5 py-0.5 rounded">
                                    B
                                </span>
                                <p className="text-xl font-extrabold text-primary mt-1">
                                    {m.valueB}
                                </p>
                            </div>
                        </div>

                        {/* Delta */}
                        <Tooltip text={m.deltaTip} position="bottom">
                            <div
                                className={`inline-flex items-center gap-1 text-xs font-bold cursor-help ${
                                    m.positive
                                        ? "text-green-600"
                                        : diff?.taxDelta === 0 &&
                                            diff?.rateDelta === 0
                                          ? "text-gray"
                                          : "text-red-600"
                                }`}
                            >
                                <DeltaIcon className="w-3.5 h-3.5" />
                                {m.delta} {m.deltaLabel}
                            </div>
                        </Tooltip>
                    </div>
                );
            })}
        </div>
    );
}
