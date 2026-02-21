"use client";

import React from "react";
import { MapPin, AlertTriangle, CheckCircle2 } from "lucide-react";

export default function ResidencyInsights({ residencyData }) {
    if (!residencyData || residencyData.length === 0) {
        return null;
    }

    return (
        <div className="space-y-4">
            <h3 className="text-2xl font-bold text-primary mb-4">
                Country Residency Status
            </h3>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {residencyData.map((country, idx) => {
                    const isResident = country.is_tax_resident;
                    const flagCode = (
                        country.country_code || "US"
                    ).toLowerCase();

                    return (
                        <div
                            key={idx}
                            className={`bg-white rounded-xl border p-5 shadow-sm transition-shadow hover:shadow-md ${
                                isResident
                                    ? "border-red-200"
                                    : "border-border-gray"
                            }`}
                        >
                            <div className="flex items-center gap-3 mb-4">
                                <img
                                    src={`https://flagcdn.com/w80/${flagCode}.png`}
                                    alt={country.country_name}
                                    className="w-10 h-10 rounded-full object-cover"
                                    loading="lazy"
                                    onError={(e) => {
                                        e.target.style.display = "none";
                                    }}
                                />
                                <div>
                                    <h4 className="font-bold text-primary text-lg leading-tight">
                                        {country.country_name}
                                    </h4>
                                    <p className="text-xs text-gray">
                                        Threshold: {country.threshold} days
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-end justify-between mt-2">
                                <div>
                                    <p className="text-3xl font-bold text-primary">
                                        {country.days_spent}
                                        <span className="text-sm font-medium text-gray ml-1">
                                            days
                                        </span>
                                    </p>
                                </div>
                                <div className="text-right">
                                    {isResident ? (
                                        <span className="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1.5 rounded-lg flex items-center gap-1.5 shadow-sm">
                                            <AlertTriangle className="w-4 h-4" />
                                            Tax Resident
                                        </span>
                                    ) : (
                                        <span className="bg-gray/10 text-gray text-xs font-semibold px-3 py-1.5 rounded-lg flex items-center gap-1.5">
                                            <CheckCircle2 className="w-4 h-4" />
                                            Non-Resident
                                        </span>
                                    )}
                                </div>
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
