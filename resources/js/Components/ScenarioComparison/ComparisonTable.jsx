"use client";

import React from "react";
import { TrendingDown, TrendingUp, Minus } from "lucide-react";

/**
 * ComparisonTable — tabular A vs B with per-country deltas and total row.
 *
 * Props:
 *   perCountry : [{ country_code, country_name, daysA, daysB, residentA, residentB, taxA, taxB, delta }]
 *   resultA    : { total_tax }
 *   resultB    : { total_tax }
 *   diff       : { taxDelta }
 *   currency   : "USD"
 */
export default function ComparisonTable({
    perCountry = [],
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

    const changeLabel = (c) => {
        const labelA =
            c.daysA === null || c.daysA === undefined
                ? "Not visited"
                : c.residentA
                  ? `${c.daysA}d Resident`
                  : `${c.daysA}d Non-Resident`;
        const labelB =
            c.daysB === null || c.daysB === undefined
                ? "Not visited"
                : c.residentB
                  ? `${c.daysB}d Resident`
                  : `${c.daysB}d Non-Resident`;
        return `${labelA} → ${labelB}`;
    };

    const DeltaCell = ({ value }) => {
        if (value === 0) {
            return (
                <span className="text-gray font-bold flex items-center gap-1">
                    <Minus className="w-3.5 h-3.5" /> —
                </span>
            );
        }
        if (value < 0) {
            return (
                <span className="text-green-600 font-bold flex items-center gap-1">
                    <TrendingDown className="w-3.5 h-3.5" />{" "}
                    {fmt(Math.abs(value))}
                </span>
            );
        }
        return (
            <span className="text-red-600 font-bold flex items-center gap-1">
                <TrendingUp className="w-3.5 h-3.5" /> {fmt(value)}
            </span>
        );
    };

    return (
        <div className="bg-white rounded-xl border border-border-gray shadow-sm overflow-hidden">
            <div className="overflow-x-auto">
                <table className="w-full text-sm">
                    <thead>
                        <tr className="bg-primary/5 text-primary border-b border-border-gray">
                            <th className="text-left py-3 px-4 font-bold text-xs uppercase tracking-wider">
                                Country / Change
                            </th>
                            <th className="text-right py-3 px-4 font-bold text-xs uppercase tracking-wider">
                                Scenario A
                            </th>
                            <th className="text-right py-3 px-4 font-bold text-xs uppercase tracking-wider">
                                Scenario B
                            </th>
                            <th className="text-right py-3 px-4 font-bold text-xs uppercase tracking-wider">
                                Difference
                            </th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-border-gray">
                        {perCountry.map((c) => (
                            <tr
                                key={c.country_code}
                                className="hover:bg-light transition-colors"
                            >
                                <td className="py-3 px-4">
                                    <div className="flex items-center gap-2">
                                        <span className="text-xs font-bold text-gray uppercase">
                                            {c.country_code}
                                        </span>
                                        <div>
                                            <p className="font-semibold text-primary">
                                                {c.country_name}
                                            </p>
                                            <p className="text-[11px] text-gray">
                                                {changeLabel(c)}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td className="py-3 px-4 text-right font-bold text-primary whitespace-nowrap">
                                    {c.daysA !== null && c.daysA !== undefined
                                        ? fmt(c.taxA)
                                        : "—"}
                                </td>
                                <td className="py-3 px-4 text-right font-bold text-primary whitespace-nowrap">
                                    {c.daysB !== null && c.daysB !== undefined
                                        ? fmt(c.taxB)
                                        : "—"}
                                </td>
                                <td className="py-3 px-4 text-right whitespace-nowrap">
                                    <DeltaCell value={c.delta} />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                    <tfoot>
                        <tr className="bg-primary/5 text-primary border-t border-border-gray font-bold">
                            <td className="py-3 px-4 uppercase text-sm font-extrabold">
                                Total
                            </td>
                            <td className="py-3 px-4 text-right text-lg">
                                {fmt(resultA?.total_tax ?? 0)}
                            </td>
                            <td className="py-3 px-4 text-right text-lg">
                                {fmt(resultB?.total_tax ?? 0)}
                            </td>
                            <td className="py-3 px-4 text-right">
                                <span
                                    className={`text-lg font-extrabold ${
                                        (diff?.taxDelta ?? 0) > 0
                                            ? "text-green-400"
                                            : (diff?.taxDelta ?? 0) < 0
                                              ? "text-red-400"
                                              : ""
                                    }`}
                                >
                                    {(diff?.taxDelta ?? 0) > 0
                                        ? "▼ "
                                        : (diff?.taxDelta ?? 0) < 0
                                          ? "▲ "
                                          : ""}
                                    {fmt(Math.abs(diff?.taxDelta ?? 0))}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    );
}
