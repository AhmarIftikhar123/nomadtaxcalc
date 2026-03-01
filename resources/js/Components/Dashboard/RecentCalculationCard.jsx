"use client";

import React from "react";
import { Link } from "@inertiajs/react";
import { Eye, RotateCcw, Globe } from "lucide-react";

/**
 * Formats a number as currency string.
 */
function fmt(amount, currency = "USD") {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency,
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
}

/**
 * RecentCalculationCard — one row in the recent calcs list.
 *
 * Props:
 *   calc : { id, tax_year, currency, gross_income, total_tax, effective_rate,
 *            countries: [{name, code}], completed_at }
 */
export default function RecentCalculationCard({ calc }) {
    const countryNames = calc.countries?.map((c) => c.name).join(", ") || "–";

    return (
        <div className="bg-white rounded-xl border border-border-gray p-5 shadow-sm hover:shadow-md transition-shadow">
            <div className="flex items-start justify-between gap-4">
                {/* Left — info */}
                <div className="min-w-0 flex-1">
                    <div className="flex items-center gap-2 mb-1.5">
                        <Globe className="w-4 h-4 text-gray flex-shrink-0" />
                        <h3 className="font-bold text-primary text-sm truncate">
                            {countryNames}
                        </h3>
                    </div>
                    <p className="text-xs text-gray mb-3">
                        Tax Year {calc.tax_year} &middot; {calc.completed_at}
                    </p>

                    {/* Metrics strip */}
                    <div className="flex flex-wrap gap-x-6 gap-y-1 text-sm">
                        <div>
                            <span className="text-gray">Income:</span>{" "}
                            <span className="font-semibold text-primary">
                                {fmt(calc.gross_income, calc.currency)}
                            </span>
                        </div>
                        <div>
                            <span className="text-gray">Tax:</span>{" "}
                            <span className="font-bold text-primary">
                                {fmt(calc.total_tax, calc.currency)}
                            </span>
                        </div>
                        <div>
                            <span className="text-gray">Rate:</span>{" "}
                            <span
                                className={`font-bold ${calc.effective_rate > 30 ? "text-red-600" : calc.effective_rate < 10 ? "text-green-600" : "text-primary"}`}
                            >
                                {calc.effective_rate}%
                            </span>
                        </div>
                    </div>
                </div>

                {/* Right — action buttons */}
                <div className="flex flex-col gap-2 flex-shrink-0">
                    <Link
                        href={route("tax-calculator.index", {
                            calculation_id: calc.id,
                        })}
                        className="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold border-2 border-primary text-primary rounded-lg hover:bg-primary hover:text-light transition-all"
                    >
                        <Eye className="w-3.5 h-3.5" />
                        View
                    </Link>
                    <Link
                        href={route("tax-calculator.index", {
                            calculation_id: calc.id,
                        })}
                        className="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold border-2 border-border-gray text-gray rounded-lg hover:border-primary hover:text-primary transition-all"
                    >
                        <RotateCcw className="w-3.5 h-3.5" />
                        Edit
                    </Link>
                </div>
            </div>
        </div>
    );
}
