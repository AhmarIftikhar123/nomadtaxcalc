"use client";

import React, { useState, useEffect, useRef } from "react";
import {
    Trash2,
    AlertTriangle,
    CheckCircle2,
    Info,
    Loader2,
} from "lucide-react";
import TaxTypeSelector from "./TaxTypeSelector";
import Select from "@/Components/Form/Select";
import web from "@/libs/axios";

export default function ResidencyPeriodItem({
    country,
    country_name,
    country_code,
    country_tax_basis,
    days,
    localIncome,
    localIncomeCurrency,
    step1Currency = "USD",
    onRemove,
    dateRange,
    isTaxResident,
    selectedTaxTypes = [],
    availableTaxTypes = [],
    onUpdate,
}) {
    const countryDisplayName = country_name || country;
    const flagCode = (country_code || "US").toLowerCase();
    const isTerritorial =
        country_tax_basis === "territorial" ||
        country_tax_basis === "remittance";

    // ─── Lazy currency fetch ──────────────────────────────────────────────────
    // Only fires once when the territorial section first becomes visible.
    const [availableCurrencies, setAvailableCurrencies] = useState([]);
    const [currenciesLoading, setCurrenciesLoading] = useState(false);
    const fetchedRef = useRef(false);

    useEffect(() => {
        if (!isTerritorial || fetchedRef.current) return;
        fetchedRef.current = true;
        setCurrenciesLoading(true);

        web.get("/currencies")
            .then((response) => {
                const data = response.data;
                // data is [{value:'EUR', label:'EUR — Euro'}, ...]
                if (Array.isArray(data) && data.length > 0) {
                    setAvailableCurrencies(data);
                } else {
                    setAvailableCurrencies([
                        { value: step1Currency, label: step1Currency },
                    ]);
                }
            })
            .catch(() => {
                // API unavailable → show only step1 currency
                setAvailableCurrencies([
                    { value: step1Currency, label: step1Currency },
                ]);
            })
            .finally(() => setCurrenciesLoading(false));
    }, [isTerritorial, step1Currency]);

    // Effective currency shown in dropdown (fallback = step1 currency)
    const activeCurrency = localIncomeCurrency || step1Currency;

    return (
        <div className="space-y-2">
            <div className="bg-light rounded-lg p-4 md:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border border-border-gray hover:border-primary hover:border-opacity-50 transition">
                <div className="flex items-center gap-4 w-full sm:flex-1">
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
                <div className="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto mt-2 sm:mt-0 border-t sm:border-t-0 border-border-gray pt-4 sm:pt-0">
                    <div className="text-left sm:text-right">
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

            {/* ── Territorial / Remittance Income Field ──────────────────────── */}
            {isTerritorial && (
                <div className="mt-4 sm:mt-0 sm:ml-16 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div className="flex items-start gap-2 mb-3">
                        <Info className="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" />
                        <p className="text-sm text-blue-800">
                            <strong>{countryDisplayName}</strong> uses{" "}
                            {country_tax_basis} taxation — only income earned{" "}
                            <em>inside</em> {countryDisplayName} is taxable.
                        </p>
                    </div>
                    <label className="block text-sm font-semibold text-blue-900 mb-2">
                        How much did you earn while in {countryDisplayName}?
                    </label>

                    <div className="flex flex-col md:flex-row md:items-center items-start gap-2">
                        {/* Amount Input */}
                        <input
                            type="number"
                            min="0"
                            step="0.01"
                            value={localIncome ?? ""}
                            onChange={(e) =>
                                onUpdate &&
                                onUpdate("local_income", e.target.value)
                            }
                            placeholder="0.00"
                            className="md:w-44 w-full px-3 py-3 border border-blue-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400 outline-none"
                        />

                        {/* Currency Selector — lazy-loaded */}
                        {currenciesLoading ? (
                            <div className="flex items-center gap-1 text-blue-500 text-sm">
                                <Loader2 className="w-4 h-4 animate-spin" />
                                <span>Loading…</span>
                            </div>
                        ) : availableCurrencies.length > 1 ? (
                            <div className="md:w-52 w-full">
                                <Select
                                    value={activeCurrency}
                                    onChange={(val) =>
                                        onUpdate &&
                                        onUpdate("local_income_currency", val)
                                    }
                                    options={availableCurrencies}
                                    placeholder="Currency…"
                                    className="md:!py-2 md:!px-4 !px-0 py-0"
                                />
                            </div>
                        ) : (
                            /* Fallback — API unavailable, show plain text */
                            <span className="text-sm text-blue-700 font-medium">
                                {activeCurrency}
                            </span>
                        )}
                    </div>

                    {activeCurrency !== step1Currency && (
                        <p className="text-xs text-blue-600 mt-2">
                            Amount will be converted from{" "}
                            <strong>{activeCurrency}</strong> to{" "}
                            <strong>{step1Currency}</strong> for tax
                            calculation.
                        </p>
                    )}

                    <p className="text-xs text-blue-600 mt-1">
                        Leave 0 if all income was earned remotely.
                    </p>
                </div>
            )}

            {/* Tax Type Selector */}
            <div className="pl-0 mt-4 sm:pl-16 sm:mt-0">
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
