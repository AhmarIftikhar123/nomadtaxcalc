'use client';

import React from 'react';
import { TrendingUp, TrendingDown, DollarSign, Percent } from 'lucide-react';

export default function ResultMetricsCards({ result }) {
    const formatCurrency = (value) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: result.currency,
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    };

    const metrics = [
        {
            label: 'Total Estimated Tax',
            value: formatCurrency(result.total_tax),
            change: result.tax_change_percent,
            changeLabel: 'vs last projection',
            icon: DollarSign,
            isPositive: result.tax_change_percent < 0,
        },
        {
            label: 'Effective Tax Rate',
            value: `${result.effective_tax_rate}%`,
            change: result.rate_change_percent,
            changeLabel: 'change',
            icon: Percent,
            isPositive: result.rate_change_percent < 0,
        },
        {
            label: 'Net Income After Tax',
            value: formatCurrency(result.net_income),
            change: result.income_change_percent,
            changeLabel: 'due to rate hike',
            icon: DollarSign,
            isPositive: result.income_change_percent > 0,
        },
    ];

    return (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            {metrics.map((metric, index) => {
                const Icon = metric.icon;
                const isPositiveChange = metric.isPositive ? metric.change > 0 : metric.change < 0;

                return (
                    <div
                        key={index}
                        className="bg-white rounded-xl border border-border-gray p-6 shadow-sm hover:shadow-md transition-shadow"
                    >
                        <div className="flex justify-between items-start mb-4">
                            <p className="text-sm text-gray font-medium">{metric.label}</p>
                            <div className="w-10 h-10 bg-primary bg-opacity-10 rounded-lg flex items-center justify-center">
                                <Icon className="w-5 h-5 text-primary" />
                            </div>
                        </div>

                        <p className="text-3xl md:text-4xl font-bold text-primary mb-3">
                            {metric.value}
                        </p>

                        <div className="flex items-center gap-2">
                            {isPositiveChange ? (
                                <>
                                    <TrendingUp className="w-4 h-4 text-green-500" />
                                    <span className="text-sm font-medium text-green-500">
                                        +{Math.abs(metric.change)}% {metric.changeLabel}
                                    </span>
                                </>
                            ) : (
                                <>
                                    <TrendingDown className="w-4 h-4 text-red-500" />
                                    <span className="text-sm font-medium text-red-500">
                                        {metric.change}% {metric.changeLabel}
                                    </span>
                                </>
                            )}
                        </div>
                    </div>
                );
            })}
        </div>
    );
}
