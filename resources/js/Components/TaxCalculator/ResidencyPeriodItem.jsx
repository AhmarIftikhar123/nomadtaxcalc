"use client";

import React from "react";
import { Trash2, AlertTriangle, CheckCircle2 } from "lucide-react";

export default function ResidencyPeriodItem({
    country,
    days,
    onRemove,
    dateRange,
    isTaxResident,
}) {
    const getCountryCode = (countryName) => {
        const countryMap = {
            "United States": "US",
            "United Kingdom": "GB",
            Canada: "CA",
            Australia: "AU",
            Germany: "DE",
            France: "FR",
            Spain: "ES",
            Italy: "IT",
            Netherlands: "NL",
            Belgium: "BE",
            Switzerland: "CH",
            Sweden: "SE",
            Norway: "NO",
            Denmark: "DK",
            Portugal: "PT",
            Thailand: "TH",
            Mexico: "MX",
            India: "IN",
            Japan: "JP",
            Singapore: "SG",
        };
        return countryMap[countryName] || "US";
    };

    return (
        <div className="bg-light rounded-lg p-4 md:p-6 flex items-center justify-between border border-border-gray hover:border-primary hover:border-opacity-50 transition">
            <div className="flex items-center gap-4 flex-1">
                {/* Country Flag */}
                <img
                    src={`https://flagcdn.com/w40/${getCountryCode(country).toLowerCase()}.png`}
                    alt={country}
                    className="w-12 h-12 rounded-full object-cover"
                    loading="lazy"
                />

                {/* Country Info */}
                <div className="flex-1">
                    <div className="flex items-center gap-2 mb-1">
                        <p className="font-bold text-primary">{country}</p>
                        {isTaxResident && (
                            <span className="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1">
                                <AlertTriangle className="w-3 h-3" />
                                Likely Tax Resident
                            </span>
                        )}
                    </div>
                    <p className="text-sm text-gray">{dateRange}</p>
                </div>
            </div>

            {/* Days Spent */}
            <div className="flex items-center gap-4">
                <div className="text-right">
                    <p className="text-2xl font-bold text-primary">{days}</p>
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
    );
}
