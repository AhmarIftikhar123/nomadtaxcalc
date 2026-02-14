'use client';

import React from 'react';
import {
    BarChart,
    Bar,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    ResponsiveContainer,
    Cell,
} from 'recharts';

export default function TaxLiabilityComparison({ comparisonData, currency }) {
    const formatCurrency = (value) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency,
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    };

    const chartData = comparisonData.map((item) => ({
        ...item,
        formatted: formatCurrency(item.liability),
    }));

    // Color based on position - primary for highest, lighter for others
    const getBarColor = (index, highestIndex) => {
        return index === highestIndex ? '#22262a' : '#b8bcc3';
    };

    const highestIndex = chartData.reduce(
        (maxIdx, item, idx) => (item.liability > chartData[maxIdx].liability ? idx : maxIdx),
        0
    );

    const CustomTooltip = ({ active, payload }) => {
        if (active && payload && payload.length) {
            return (
                <div className="bg-primary text-light px-3 py-2 rounded-lg text-sm font-medium shadow-lg">
                    <p>{payload[0].payload.country}</p>
                    <p>{payload[0].payload.formatted}</p>
                </div>
            );
        }
        return null;
    };

    return (
        <div className="bg-white rounded-xl border border-border-gray p-6 md:p-8 mb-12 shadow-sm">
            <div className="mb-6">
                <h3 className="text-2xl font-bold text-primary mb-2">
                    Tax Liability Comparison
                </h3>
                <p className="text-gray">
                    Comparing your estimated liability across entered countries.
                </p>
            </div>

            <div className="w-full h-80">
                <ResponsiveContainer width="100%" height="100%">
                    <BarChart data={chartData} margin={{ top: 20, right: 30, left: 0, bottom: 50 }}>
                        <CartesianGrid
                            strokeDasharray="3 3"
                            stroke="#e0e0e1"
                            vertical={false}
                        />
                        <XAxis
                            dataKey="country"
                            tick={{ fill: '#737578', fontSize: 12 }}
                            axisLine={{ stroke: '#e0e0e1' }}
                        />
                        <YAxis
                            tick={{ fill: '#737578', fontSize: 12 }}
                            axisLine={{ stroke: '#e0e0e1' }}
                            tickFormatter={(value) => `${(value / 1000).toFixed(0)}k`}
                        />
                        <Tooltip content={<CustomTooltip />} />
                        <Bar dataKey="liability" radius={[8, 8, 0, 0]}>
                            {chartData.map((entry, index) => (
                                <Cell
                                    key={`cell-${index}`}
                                    fill={getBarColor(index, highestIndex)}
                                />
                            ))}
                        </Bar>
                    </BarChart>
                </ResponsiveContainer>
            </div>

            {/* Legend/Details below chart */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                {chartData.map((item) => (
                    <div
                        key={item.country}
                        className="text-center p-4 bg-light rounded-lg"
                    >
                        <p className="text-sm text-gray mb-1">{item.country}</p>
                        <p className="text-lg font-bold text-primary">
                            {item.formatted}
                        </p>
                    </div>
                ))}
            </div>
        </div>
    );
}
