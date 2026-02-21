"use client";

import React from "react";
import { TrendingUp, TrendingDown, DollarSign, Percent } from "lucide-react";

export default function ResultMetricsCards({ result }) {
    const formatCurrency = (value) => {
        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: result.currency,
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    };

    const metrics = [
        {
            label: "Total Estimated Tax",
            value: formatCurrency(result.total_tax),
            icon: DollarSign,
        },
        {
            label: "Effective Tax Rate",
            value: `${result.effective_tax_rate}%`,
            icon: Percent,
        },
        {
            label: "Net Income After Tax",
            value: formatCurrency(result.net_income),
            icon: DollarSign,
        },
    ];

    return (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
            {metrics.map((metric, index) => {
                const Icon = metric.icon;

                return (
                    <div
                        key={index}
                        className="bg-white rounded-xl border border-border-gray p-6 shadow-sm hover:shadow-md transition-shadow"
                    >
                        <div className="flex justify-between items-start mb-4">
                            <p className="text-sm text-gray font-medium">
                                {metric.label}
                            </p>
                            <div className="w-10 h-10 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center">
                                <Icon className="w-5 h-5 text-primary" />
                            </div>
                        </div>

                        <p className="text-3xl md:text-4xl font-bold text-primary mb-3">
                            {metric.value}
                        </p>
                    </div>
                );
            })}
        </div>
    );
}
