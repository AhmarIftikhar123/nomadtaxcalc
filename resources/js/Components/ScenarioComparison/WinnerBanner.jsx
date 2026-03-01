"use client";

import React from "react";
import { Trophy, Equal } from "lucide-react";

/**
 * WinnerBanner — hero section announcing the comparison result.
 *
 * Props:
 *   diff     : { winner: 'A'|'B'|'tie', savings, perCountry }
 *   currency : "USD"
 */
export default function WinnerBanner({ diff, currency = "USD" }) {
    const fmt = (val) =>
        new Intl.NumberFormat("en-US", {
            style: "currency",
            currency,
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(val);

    if (diff.winner === "tie") {
        return (
            <div className="bg-gray/5 border border-border-gray text-primary rounded-xl p-6 flex items-center gap-4">
                <div className="w-14 h-14 rounded-xl bg-white border border-border-gray flex items-center justify-center flex-shrink-0">
                    <Equal className="w-8 h-8 text-gray" />
                </div>
                <div>
                    <h2 className="text-xl font-extrabold">
                        Both scenarios are equal
                    </h2>
                    <p className="text-sm text-gray mt-1 font-medium">
                        No tax difference between scenarios — consider other
                        factors.
                    </p>
                </div>
            </div>
        );
    }

    const winnerLabel = diff.winner === "A" ? "Scenario A" : "Scenario B";

    // Build a simple insight from the top per-country delta
    const topDelta = [...(diff.perCountry || [])].sort(
        (a, b) => Math.abs(b.delta) - Math.abs(a.delta),
    )[0];

    let insight = "";
    if (topDelta) {
        const daysA = topDelta.daysA ?? 0;
        const daysB = topDelta.daysB ?? 0;
        const dayDiff = Math.abs(daysA - daysB);
        if (topDelta.residentA && !topDelta.residentB) {
            insight = `${dayDiff} fewer days in ${topDelta.country_name} drops you below the threshold — no tax residency triggered`;
        } else if (!topDelta.residentA && topDelta.residentB) {
            insight = `${dayDiff} more days in ${topDelta.country_name} triggers tax residency`;
        } else {
            insight = `Day adjustments in ${topDelta.country_name} changed your tax liability`;
        }
    }

    return (
        <div className="bg-green-50/50 border border-green-200 text-primary rounded-xl p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div className="w-14 h-14 rounded-xl bg-white border border-green-100 flex items-center justify-center flex-shrink-0 shadow-sm">
                <Trophy className="w-8 h-8 text-yellow-500" />
            </div>
            <div className="flex-1 min-w-0">
                <h2 className="text-xl font-extrabold">
                    {winnerLabel} saves you significantly more money
                </h2>
                {insight && (
                    <p className="text-sm text-gray/80 mt-1 font-medium">
                        {insight}
                    </p>
                )}
            </div>
            <div className="bg-green-500 text-white px-5 py-3 rounded-xl text-center flex-shrink-0">
                <p className="text-xl font-extrabold">{fmt(diff.savings)}</p>
                <p className="text-[10px] font-bold uppercase tracking-wider opacity-80">
                    saved vs {diff.winner === "A" ? "Scenario B" : "Scenario A"}
                </p>
            </div>
        </div>
    );
}
