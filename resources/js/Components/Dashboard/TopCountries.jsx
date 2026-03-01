"use client";

import React from "react";
import { MapPin } from "lucide-react";

/**
 * TopCountries — ranked list of most frequently computed countries.
 *
 * Props:
 *   countries : [{ name, code, count }]
 */
export default function TopCountries({ countries = [] }) {
    if (countries.length === 0) return null;

    const maxCount = Math.max(...countries.map((c) => c.count), 1);

    return (
        <div className="bg-white rounded-xl border border-border-gray p-5 shadow-sm">
            <div className="flex items-center gap-2 mb-4">
                <MapPin className="w-4 h-4 text-gray" />
                <h3 className="text-xs font-semibold text-gray uppercase tracking-wider">
                    Top Countries
                </h3>
            </div>
            <div className="space-y-2.5">
                {countries.map((country, idx) => (
                    <div key={country.code} className="flex items-center gap-3">
                        <span className="text-sm font-bold text-gray w-5 text-right">
                            {idx + 1}.
                        </span>
                        <div className="flex-1 min-w-0">
                            <div className="flex items-center justify-between mb-1">
                                <span className="text-sm font-semibold text-primary truncate">
                                    {country.name}
                                </span>
                                <span className="text-xs font-bold text-gray ml-2 flex-shrink-0">
                                    {country.count}
                                </span>
                            </div>
                            <div className="bg-light rounded-full h-1.5 overflow-hidden">
                                <div
                                    className="bg-primary/60 h-full rounded-full transition-all duration-500"
                                    style={{
                                        width: `${(country.count / maxCount) * 100}%`,
                                    }}
                                />
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}
