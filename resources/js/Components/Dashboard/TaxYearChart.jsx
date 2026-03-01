"use client";

import React from "react";
import { Calendar } from "lucide-react";

/**
 * TaxYearChart — horizontal bar chart showing calculations per tax year.
 *
 * Props:
 *   yearBreakdown : { 2026: 8, 2025: 4, ... }
 */
export default function TaxYearChart({ yearBreakdown = {} }) {
    const entries = Object.entries(yearBreakdown)
        .map(([year, count]) => ({ year: Number(year), count }))
        .sort((a, b) => b.year - a.year);

    if (entries.length === 0) return null;

    const maxCount = Math.max(...entries.map((e) => e.count), 1);

    return (
        <div className="bg-white rounded-xl border border-border-gray p-5 shadow-sm">
            <div className="flex items-center gap-2 mb-4">
                <Calendar className="w-4 h-4 text-gray" />
                <h3 className="text-xs font-semibold text-gray uppercase tracking-wider">
                    Tax Year Breakdown
                </h3>
            </div>
            <div className="space-y-3">
                {entries.map(({ year, count }) => (
                    <div key={year} className="flex items-center gap-3">
                        <span className="text-sm font-bold text-primary w-12 text-right">
                            {year}
                        </span>
                        <div className="flex-1 bg-light rounded-full h-5 overflow-hidden">
                            <div
                                className="bg-primary h-full rounded-full transition-all duration-500"
                                style={{
                                    width: `${(count / maxCount) * 100}%`,
                                }}
                            />
                        </div>
                        <span className="text-sm font-bold text-primary w-6 text-right">
                            {count}
                        </span>
                    </div>
                ))}
            </div>
        </div>
    );
}
