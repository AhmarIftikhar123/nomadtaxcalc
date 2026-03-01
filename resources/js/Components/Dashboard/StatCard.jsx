"use client";

import React from "react";

/**
 * StatCard — displays a single metric (value + label + optional icon).
 *
 * Props:
 *   icon      : lucide-react component
 *   label     : string  e.g. "Total Calculations"
 *   value     : string | number  e.g. "12" or "24.3%"
 *   accent    : string  optional Tailwind text-color class e.g. "text-green-600"
 */
export default function StatCard({ icon: Icon, label, value, accent = "" }) {
    return (
        <div className="bg-white rounded-xl border border-border-gray p-5 shadow-sm flex items-start gap-4 hover:shadow-md transition-shadow">
            {Icon && (
                <div className="w-11 h-11 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <Icon className="w-5 h-5 text-primary" />
                </div>
            )}
            <div className="min-w-0">
                <p className="text-xs font-semibold text-gray uppercase tracking-wider mb-1">
                    {label}
                </p>
                <p
                    className={`text-2xl font-extrabold leading-tight ${accent || "text-primary"}`}
                >
                    {value}
                </p>
            </div>
        </div>
    );
}
