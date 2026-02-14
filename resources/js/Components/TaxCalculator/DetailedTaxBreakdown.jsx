"use client";

import React from "react";
import { Download, Eye } from "lucide-react";

export default function DetailedTaxBreakdown({ breakdownData, currency }) {
    const formatCurrency = (value) => {
        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: currency,
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    };

    const handleDownloadCSV = () => {
        const headers = [
            "Country",
            "Income Source",
            "Taxable Amount",
            "Tax Rate",
            "Liability",
        ];
        const rows = breakdownData.map((item) => [
            item.country_name,
            "Global Income",
            formatCurrency(item.taxable_income),
            `${item.effective_rate}%`,
            formatCurrency(item.tax_due),
        ]);

        const csvContent = [
            headers.join(","),
            ...rows.map((row) => row.map((cell) => `"${cell}"`).join(",")),
        ].join("\n");

        const blob = new Blob([csvContent], { type: "text/csv" });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download = "tax-breakdown.csv";
        link.click();
        window.URL.revokeObjectURL(url);
    };

    return (
        <div className="bg-white rounded-xl border border-border-gray p-6 md:p-8 shadow-sm">
            <div className="flex items-center justify-between mb-6">
                <div>
                    <h3 className="text-2xl font-bold text-primary">
                        Detailed Tax Breakdown
                    </h3>
                </div>
                <button
                    onClick={handleDownloadCSV}
                    className="flex items-center gap-2 px-4 py-2 text-primary font-semibold border-2 border-primary rounded-lg hover:bg-primary hover:text-light transition-colors"
                >
                    <Download className="w-4 h-4" />
                    Download CSV
                </button>
            </div>

            <div className="overflow-x-auto">
                <table className="w-full">
                    <thead>
                        <tr className="border-b-2 border-border-gray">
                            <th className="text-left py-4 px-4 font-bold text-primary text-sm uppercase tracking-wide">
                                Country
                            </th>
                            <th className="text-left py-4 px-4 font-bold text-primary text-sm uppercase tracking-wide">
                                Income Source
                            </th>
                            <th className="text-right py-4 px-4 font-bold text-primary text-sm uppercase tracking-wide">
                                Taxable Amt.
                            </th>
                            <th className="text-right py-4 px-4 font-bold text-primary text-sm uppercase tracking-wide">
                                Tax Rate
                            </th>
                            <th className="text-right py-4 px-4 font-bold text-primary text-sm uppercase tracking-wide">
                                Liability
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {breakdownData.map((item, index) => (
                            <tr
                                key={index}
                                className="border-b border-border-gray hover:bg-light transition-colors"
                            >
                                <td className="py-4 px-4">
                                    <div className="flex items-center gap-3">
                                        {/* Flag handled by backend or CSS class if available, omitted for now */}
                                        <span className="font-medium text-primary">
                                            {item.country_name}
                                        </span>
                                    </div>
                                </td>
                                <td className="py-4 px-4 text-gray text-sm">
                                    Global Income
                                </td>
                                <td className="py-4 px-4 text-right text-primary font-medium">
                                    {formatCurrency(item.taxable_income)}
                                </td>
                                <td className="py-4 px-4 text-right text-primary font-medium">
                                    {item.effective_rate}%
                                </td>
                                <td className="py-4 px-4 text-right">
                                    <span className="font-bold text-primary bg-primary bg-opacity-10 px-3 py-1 rounded-lg">
                                        {formatCurrency(item.tax_due)}
                                    </span>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                    <tfoot>
                        <tr className="bg-light border-t-2 border-primary">
                            <td
                                colSpan="4"
                                className="py-4 px-4 font-bold text-primary text-right"
                            >
                                Total Tax Liability
                            </td>
                            <td className="py-4 px-4 text-right">
                                <span className="text-lg font-bold text-primary bg-primary bg-opacity-10 px-3 py-1 rounded-lg">
                                    {formatCurrency(
                                        breakdownData.reduce(
                                            (sum, item) =>
                                                sum + Number(item.tax_due),
                                            0,
                                        ),
                                    )}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    );
}
