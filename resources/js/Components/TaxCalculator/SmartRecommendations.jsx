"use client";

import React, { useState } from "react";
import { Link } from "@inertiajs/react";
import {
    Zap,
    MessageSquare,
    UserCheck,
    ExternalLink,
    ChevronDown,
} from "lucide-react";

export default function SmartRecommendations({ recommendations, currency }) {
    const [expandedIndex, setExpandedIndex] = useState(0);

    const formatCurrency = (value) => {
        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: currency,
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center gap-2 mb-6">
                <Zap className="w-6 h-6 text-primary" />
                <h3 className="text-2xl font-bold text-primary">
                    Smart Recommendations
                </h3>
            </div>

            {/* Recommendations List */}
            <div className="space-y-4">
                {recommendations.map((recommendation, index) => (
                    <div
                        key={index}
                        className={`rounded-xl overflow-hidden border transition-all cursor-pointer ${
                            expandedIndex === index
                                ? "border-primary bg-primary bg-opacity-5"
                                : "border-border-gray bg-white"
                        }`}
                    >
                        <button
                            onClick={() =>
                                setExpandedIndex(
                                    expandedIndex === index ? -1 : index,
                                )
                            }
                            className="w-full p-6 text-left hover:bg-opacity-50 transition-colors"
                        >
                            <div className="flex items-start gap-4">
                                <div className="w-12 h-12 bg-primary text-light rounded-lg flex items-center justify-center flex-shrink-0">
                                    <Zap className="w-6 h-6" />
                                </div>
                                <div className="flex-1">
                                    <div className="flex items-start justify-between gap-4">
                                        <div>
                                            <p className="text-xs font-bold text-primary uppercase tracking-wide mb-1">
                                                {recommendation.type.split('_').join(' ')}
                                            </p>
                                            <h4 className="text-lg font-bold text-primary">
                                                {recommendation.title}
                                            </h4>
                                          
                                        </div>
                                        <ChevronDown
                                            className={`w-5 h-5 text-primary flex-shrink-0 mt-1 transition-transform ${
                                                expandedIndex === index
                                                    ? "rotate-180"
                                                    : ""
                                            }`}
                                        />
                                    </div>
                                    {recommendation.savings && (
                                        <div className="mt-3 inline-flex items-center gap-2 bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                                            Save{" "}
                                            {formatCurrency(
                                                recommendation.savings,
                                            )}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </button>

                        {/* Expanded Details */}
                        {expandedIndex === index && (
                            <div className="border-t border-border-gray px-6 py-4 bg-light">
                                <p className="text-sm text-gray leading-relaxed mb-4">
                                    {recommendation.details ||
                                        recommendation.message}
                                </p>

                                {recommendation.actionItems &&
                                    recommendation.actionItems.length > 0 && (
                                        <div className="bg-white rounded-lg p-4 mb-4 border border-border-gray">
                                            <p className="text-xs text-gray uppercase font-bold tracking-wide mb-2">
                                                Action Items
                                            </p>
                                            <ul className="text-sm text-primary space-y-2">
                                                {recommendation.actionItems.map(
                                                    (item, idx) => (
                                                        <li
                                                            key={idx}
                                                            className="flex gap-2"
                                                        >
                                                            <span className="text-primary">
                                                                •
                                                            </span>
                                                            <span>{item}</span>
                                                        </li>
                                                    ),
                                                )}
                                            </ul>
                                        </div>
                                    )}

                                <div className="flex gap-3">
                                    <Link href={route("tax-calculator.index", { scenario_comparison: "true" })} className="flex-1 px-4 py-2 bg-primary text-light font-semibold rounded-lg hover:bg-dark transition-colors flex items-center justify-center gap-2">
                                        Compare Scenarios
                                        <ExternalLink className="w-4 h-4" />
                                    </Link>
                                </div>
                            </div>
                        )}
                    </div>
                ))}
            </div>

            {/* Professional Help */}
            <div className="bg-primary text-light rounded-xl p-6 md:p-8">
                <div className="flex items-start gap-4">
                    <UserCheck className="w-8 h-8 flex-shrink-0" />
                    <div className="flex-1">
                        <h4 className="text-lg font-bold mb-2">
                            Need professional help?
                        </h4>
                        <p className="text-sm mb-4 opacity-90">
                            Book a 30-min consultation with a cross-border tax
                            specialist to optimize your tax situation.
                        </p>
                        <button className="px-4 py-2 bg-light text-primary font-semibold rounded-lg hover:bg-opacity-90 transition-colors flex items-center gap-2">
                            Book Consultation
                            <ExternalLink className="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
