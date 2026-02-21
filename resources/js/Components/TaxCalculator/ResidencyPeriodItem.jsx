"use client";

import React from "react";
import { Trash2, AlertTriangle, CheckCircle2 } from "lucide-react";
import TaxTypeSelector from "./TaxTypeSelector";
export default function ResidencyPeriodItem({
    country,
    country_name,
    country_code,
    days,
    onRemove,
    dateRange,
    isTaxResident,
    selectedTaxTypes = [],
    availableTaxTypes = [],
    onUpdate,
}) {
    const countryDisplayName = country_name || country;
    // Use ISO code directly from backend — no hardcoded map needed
    const flagCode = (country_code || "US").toLowerCase();

    return (
        <div className="space-y-2">
            <div className="bg-light rounded-lg p-4 md:p-6 flex items-center justify-between border border-border-gray hover:border-primary hover:border-opacity-50 transition">
                <div className="flex items-center gap-4 flex-1">
                    {/* Country Flag */}
                    <img
                        src={`https://flagcdn.com/w80/${flagCode}.png`}
                        alt={countryDisplayName}
                        className="w-12 h-12 rounded-full object-cover"
                        loading="lazy"
                        onError={(e) => {
                            e.target.style.display = "none";
                        }}
                    />

                    {/* Country Info */}
                    <div className="flex-1">
                        <div className="flex items-center gap-2 mb-1">
                            <p className="font-bold text-primary">
                                {countryDisplayName}
                            </p>
                            {isTaxResident ? (
                                <span className="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1.5 shadow-sm">
                                    <AlertTriangle className="w-3.5 h-3.5" />
                                    Likely Tax Resident
                                </span>
                            ) : (
                                <span className="bg-gray/10 text-gray text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1.5">
                                    <CheckCircle2 className="w-3.5 h-3.5" />
                                    Below Threshold
                                </span>
                            )}
                        </div>
                        <p className="text-sm text-gray">{dateRange}</p>
                    </div>
                </div>

                {/* Days Spent */}
                <div className="flex items-center gap-4">
                    <div className="text-right">
                        <p className="text-2xl font-bold text-primary">
                            {days}
                        </p>
                        <p className="text-xs text-gray uppercase font-semibold">
                            Days
                        </p>
                    </div>

                    {/* Remove Button */}
                    <button
                        onClick={onRemove}
                        className="p-2 hover:bg-white rounded-lg text-gray hover:text-red-500 transition"
                        title="Remove this residency period"
                    >
                        <Trash2 className="w-5 h-5" />
                    </button>
                </div>
            </div>

            {/* Tax Type Selector */}
            <div className="pl-16">
                <TaxTypeSelector
                    countryName={countryDisplayName}
                    value={selectedTaxTypes}
                    onChange={(newTypes) =>
                        onUpdate && onUpdate("selected_tax_types", newTypes)
                    }
                    availableTaxTypes={availableTaxTypes}
                />
            </div>
        </div>
    );
}
