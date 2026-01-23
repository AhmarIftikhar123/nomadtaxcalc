'use client';

import React, { useState } from 'react';
import { AlertTriangle, MapPin, ChevronRight } from 'lucide-react';

export default function ResidencyRiskAlert({ residencyData }) {
    const [showDetails, setShowDetails] = useState(false);

    if (!residencyData || residencyData.days_in_country < 150) {
        return null;
    }

    const riskLevel =
        residencyData.days_in_country >= 183 ? 'high' : 'warning';
    const daysRemaining = 183 - residencyData.days_in_country;

    return (
        <div className="mb-12">
            <div
                className={`rounded-xl p-6 md:p-8 border-l-4 ${
                    riskLevel === 'high'
                        ? 'bg-red-50 border-red-500'
                        : 'bg-yellow-50 border-yellow-500'
                }`}
            >
                <div className="flex items-start gap-4">
                    <div className="flex-shrink-0">
                        <AlertTriangle
                            className={`w-6 h-6 ${
                                riskLevel === 'high'
                                    ? 'text-red-600'
                                    : 'text-yellow-600'
                            }`}
                        />
                    </div>
                    <div className="flex-1">
                        <h3
                            className={`text-lg font-bold mb-2 ${
                                riskLevel === 'high'
                                    ? 'text-red-700'
                                    : 'text-yellow-800'
                            }`}
                        >
                            {riskLevel === 'high'
                                ? 'Residency Risk Alert'
                                : 'Residency Warning'}
                        </h3>
                        <p
                            className={`text-sm leading-relaxed ${
                                riskLevel === 'high'
                                    ? 'text-red-700'
                                    : 'text-yellow-700'
                            }`}
                        >
                            You have spent{' '}
                            <strong>
                                {residencyData.days_in_country} days
                            </strong>{' '}
                            in {residencyData.country_name}. You are{' '}
                            {riskLevel === 'high'
                                ? 'exceeding the 183-day tax residency threshold'
                                : `approaching the 183-day tax residency threshold (${daysRemaining} days remaining)`}
                            , which may trigger full tax liability on worldwide
                            income.
                        </p>
                        <button
                            onClick={() => setShowDetails(!showDetails)}
                            className={`mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-colors ${
                                riskLevel === 'high'
                                    ? 'bg-red-100 text-red-700 hover:bg-red-200'
                                    : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'
                            }`}
                        >
                            View Risk Details
                            <ChevronRight className="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            {showDetails && (
                <div className="mt-6 bg-white rounded-xl border border-border-gray p-6 md:p-8">
                    <h4 className="text-lg font-bold text-primary mb-4">
                        Risk Details
                    </h4>
                    <div className="space-y-4">
                        <div className="flex items-start gap-3">
                            <MapPin className="w-5 h-5 text-primary flex-shrink-0 mt-0.5" />
                            <div>
                                <p className="font-semibold text-primary">
                                    Residency Threshold
                                </p>
                                <p className="text-sm text-gray">
                                    Most countries consider you a tax resident
                                    if you spend 183 or more days in a fiscal
                                    year.
                                </p>
                            </div>
                        </div>
                        <div className="flex items-start gap-3">
                            <AlertTriangle className="w-5 h-5 text-primary flex-shrink-0 mt-0.5" />
                            <div>
                                <p className="font-semibold text-primary">
                                    Worldwide Income Liability
                                </p>
                                <p className="text-sm text-gray">
                                    Tax residents are typically liable to pay
                                    taxes on all worldwide income, not just
                                    income earned in that country.
                                </p>
                            </div>
                        </div>
                        <div className="flex items-start gap-3">
                            <MapPin className="w-5 h-5 text-primary flex-shrink-0 mt-0.5" />
                            <div>
                                <p className="font-semibold text-primary">
                                    Actions to Consider
                                </p>
                                <ul className="text-sm text-gray list-disc list-inside mt-1">
                                    <li>Optimize residency in lower-tax jurisdictions</li>
                                    <li>Consult with a tax professional</li>
                                    <li>Review treaty benefits if applicable</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
