"use client";

import React, { useState } from "react";
import Select from "@/Components/Form/Select";
import Tooltip from "@/Components/Ui/Tooltip";
import {
    Plus,
    Minus,
    Trash2,
    AlertTriangle,
    CheckCircle2,
    ShieldOff,
} from "lucide-react";

/**
 * ScenarioPanel — editable list of country rows with day adjusters.
 *
 * Props:
 *   label           : "Scenario A" | "Scenario B"
 *   badge           : "CURRENT" | "NEW"
 *   periods         : [{ country_id, country_code, country_name, days_spent, tax_residency_days }]
 *   onUpdatePeriod  : (index, field, value) => void
 *   onRemovePeriod  : (index) => void
 *   onAddPeriod     : (countryOption) => void
 *   countries       : [{ value, label, ... }]  — full list for Select
 *   accentColor     : border color class e.g. "border-green-500" or "border-blue-500"
 */
export default function ScenarioPanel({
    label,
    badge,
    periods = [],
    onUpdatePeriod,
    onRemovePeriod,
    onAddPeriod,
    countries = [],
    accentColor = "border-primary",
}) {
    const [addingCountry, setAddingCountry] = useState(false);

    const totalDays = periods.reduce(
        (sum, p) => sum + (Number(p.days_spent) || 0),
        0,
    );
    const isValid = totalDays === 365;

    // Filter out countries already in this scenario
    const usedIds = new Set(periods.map((p) => Number(p.country_id)));
    const availableCountries = countries.filter(
        (c) => !usedIds.has(Number(c.value)),
    );

    const handleAddCountry = (countryId) => {
        if (!countryId) return;
        const country = countries.find(
            (c) => String(c.value) === String(countryId),
        );
        if (country) {
            onAddPeriod({
                country_id: country.value,
                country_code: country.code || "",
                country_name: country.label,
                days_spent: 0,
                tax_residency_days: country.tax_residency_days || 183,
            });
            setAddingCountry(false);
        }
    };

    const getResidencyInfo = (period) => {
        const days = Number(period.days_spent) || 0;
        const threshold = Number(period.tax_residency_days) || 183;
        const diff = threshold - days;

        if (days >= threshold) {
            return {
                label: "Tax Resident",
                tip: `${days}d exceeds ${threshold}d threshold — worldwide income taxable`,
                color: "text-green-700 bg-green-100",
                icon: CheckCircle2,
            };
        }
        if (diff <= 20 && days > 0) {
            return {
                label: "Near Threshold",
                tip: `${days}d is only ${diff}d below the ${threshold}d threshold — risky`,
                color: "text-amber-700 bg-amber-100",
                icon: AlertTriangle,
            };
        }
        if (days === 0) {
            return {
                label: "Not visited",
                tip: "0 days — no tax obligation in this country",
                color: "text-gray bg-light",
                icon: ShieldOff,
            };
        }
        return {
            label: "Non-Resident",
            tip: `${days}d is ${diff}d below the ${threshold}d threshold — safe`,
            color: "text-primary bg-light",
            icon: ShieldOff,
        };
    };

    return (
        <div
            className={`bg-white rounded-xl border-2 ${accentColor} shadow-sm `}
        >
            {/* Header */}
            <div className="flex items-center justify-between px-5 py-3 bg-primary/5 text-primary border-b border-primary/10 overflow-hidden rounded-t-xl">
                <div className="flex items-center gap-2.5">
                    <h3 className="font-bold text-sm">{label}</h3>
                    <span className="text-[10px] font-bold uppercase tracking-wider bg-white border border-border-gray text-primary shadow-sm px-2 py-0.5 rounded-full">
                        {badge}
                    </span>
                </div>
                <span className="text-xs text-primary/60 font-medium">
                    {badge === "CURRENT" ? "Your actual" : "What if…"}
                </span>
            </div>

            {/* Country Rows */}
            <div className="p-4 space-y-3">
                {periods.map((period, idx) => {
                    const info = getResidencyInfo(period);
                    const StatusIcon = info.icon;

                    return (
                        <div
                            key={`${period.country_id}-${idx}`}
                            className="flex flex-col sm:flex-row sm:items-center p-3 rounded-lg border border-border-gray hover:border-primary/30 transition-colors gap-3"
                        >
                            <div className="flex items-center gap-3 flex-1 min-w-0">
                                {/* Country code badge */}
                                <span className="text-xs font-bold text-gray uppercase w-7 flex-shrink-0">
                                    {period.country_code || "—"}
                                </span>

                                {/* Country info */}
                                <div className="flex-1 min-w-0">
                                    <p className="text-sm font-semibold text-primary truncate leading-tight">
                                        {period.country_name}
                                    </p>
                                    <Tooltip text={info.tip} position="bottom">
                                        <span
                                            className={`inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded mt-0.5 cursor-help ${info.color}`}
                                        >
                                            <StatusIcon className="w-3 h-3" />
                                            {info.label}
                                        </span>
                                    </Tooltip>
                                </div>
                            </div>

                            {/* Mobile action bar */}
                            <div className="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto pl-10 sm:pl-0 sm:mt-0 pt-2 sm:pt-0 border-t border-border-gray/30 sm:border-0">
                                {/* Day stepper */}
                                <div className="flex items-center gap-1 flex-shrink-0">
                                    <Tooltip
                                        text={`Decrease days in ${period.country_name}`}
                                        position="top"
                                    >
                                        <button
                                            type="button"
                                            onClick={() =>
                                                onUpdatePeriod(
                                                    idx,
                                                    "days_spent",
                                                    Math.max(
                                                        0,
                                                        Number(
                                                            period.days_spent,
                                                        ) - 1,
                                                    ),
                                                )
                                            }
                                            className="w-8 h-8 flex items-center justify-center rounded-lg border border-border-gray text-gray hover:border-primary hover:text-primary transition-all"
                                        >
                                            <Minus className="w-3.5 h-3.5" />
                                        </button>
                                    </Tooltip>

                                    <input
                                        type="number"
                                        max="365"
                                        value={period.days_spent}
                                        onChange={(e) => {
                                            const val = e.target.value;

                                            if (val === "") {
                                                onUpdatePeriod(
                                                    idx,
                                                    "days_spent",
                                                    "",
                                                );
                                                return;
                                            }

                                            const num = Math.min(
                                                365,
                                                Math.max(0, Number(val)),
                                            );
                                            onUpdatePeriod(
                                                idx,
                                                "days_spent",
                                                num,
                                            );
                                        }}
                                        className="w-20 h-8 text-center text-sm font-bold text-primary border border-border-gray rounded-lg focus:border-primary focus:ring-1 focus:ring-primary outline-none"
                                    />

                                    <Tooltip
                                        text={`Increase days in ${period.country_name}`}
                                        position="top"
                                    >
                                        <button
                                            type="button"
                                            onClick={() =>
                                                onUpdatePeriod(
                                                    idx,
                                                    "days_spent",
                                                    Math.min(
                                                        365,
                                                        Number(
                                                            period.days_spent,
                                                        ) + 1,
                                                    ),
                                                )
                                            }
                                            className="w-8 h-8 flex items-center justify-center rounded-lg border border-border-gray text-gray hover:border-primary hover:text-primary transition-all"
                                        >
                                            <Plus className="w-3.5 h-3.5" />
                                        </button>
                                    </Tooltip>

                                    <span className="text-xs text-gray ml-1 w-8">
                                        days
                                    </span>
                                </div>

                                {/* Remove */}
                                <Tooltip text="Remove country" position="top">
                                    <button
                                        type="button"
                                        onClick={() => onRemovePeriod(idx)}
                                        className="w-7 h-7 flex items-center justify-center text-gray hover:text-red-500 transition-colors flex-shrink-0"
                                    >
                                        <Trash2 className="w-3.5 h-3.5" />
                                    </button>
                                </Tooltip>
                            </div>
                        </div>
                    );
                })}

                {/* Total row */}
                <div
                    className={`flex items-center justify-between px-3 py-2 rounded-lg text-sm font-bold ${isValid ? "bg-green-50 text-green-700" : "bg-amber-50 text-amber-700"}`}
                >
                    <span>Total days</span>
                    <span className="flex items-center gap-1">
                        {totalDays} / 365
                        {isValid ? (
                            <CheckCircle2 className="w-4 h-4" />
                        ) : (
                            <Tooltip
                                text="Total must equal 365 for an accurate comparison"
                                position="top"
                            >
                                <AlertTriangle className="w-4 h-4 cursor-help" />
                            </Tooltip>
                        )}
                    </span>
                </div>

                {/* Add Country */}
                {addingCountry ? (
                    <div className="space-y-2">
                        <Select
                            label=""
                            value=""
                            onChange={handleAddCountry}
                            options={availableCountries}
                            placeholder="Search country..."
                        />
                        <button
                            type="button"
                            onClick={() => setAddingCountry(false)}
                            className="text-xs ml-2 text-white bg-primary px-4 py-2 font-bold rounded-lg hover:text-gray transition-colors"
                        >
                            Cancel
                        </button>
                    </div>
                ) : (
                    <button
                        type="button"
                        onClick={() => setAddingCountry(true)}
                        className="w-full py-3 border-2 border-dashed border-border-gray rounded-lg text-sm font-semibold text-gray hover:border-primary hover:text-primary transition-all flex items-center justify-center gap-1.5"
                    >
                        <Plus className="w-4 h-4" />
                        Add Country to {label?.split(" ")[1] || ""}
                    </button>
                )}
            </div>
        </div>
    );
}
