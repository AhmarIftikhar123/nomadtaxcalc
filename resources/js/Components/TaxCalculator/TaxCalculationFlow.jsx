"use client";

import React, { useState } from "react";
import {
    Calculator,
    ChevronDown,
    ChevronRight,
    MapPin,
    ArrowRight,
    Minus,
    Plus,
    Equal,
    AlertCircle,
    CheckCircle2,
    XCircle,
    Globe,
} from "lucide-react";

export default function TaxCalculationFlow({ result }) {
    const [isOpen, setIsOpen] = useState(true);
    const formatCurrency = (value) => {
        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: result.currency,
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    };

    const {
        residency_data = [],
        breakdown_by_country = [],
        total_tax,
        net_income,
        tax_year,
    } = result;

    // Helper to find tax calculation details for a country
    const getTaxDetails = (countryId) => {
        return breakdown_by_country.find((c) => c.country_id === countryId);
    };

    return (
        <div className="bg-white rounded-xl border border-border-gray shadow-sm mb-4 overflow-hidden">
            {/* Collapsible Header */}
            <button
                onClick={() => setIsOpen(!isOpen)}
                className="w-full flex items-center justify-between p-6 hover:bg-light transition-colors text-left"
            >
                <div className="flex items-center gap-3">
                    <div className="w-10 h-10 bg-primary text-light rounded-lg flex items-center justify-center">
                        <Calculator
                            className="w-5 h-5"
                            strokeWidth={2}
                            color="#ffffff"
                        />
                    </div>
                    <div>
                        <h3 className="text-lg font-bold text-primary">
                            How We Calculated Your Tax
                        </h3>
                        <p className="text-sm text-gray">
                            See the mathematical breakdown of your tax
                            liability.
                        </p>
                    </div>
                </div>
                {isOpen ? (
                    <ChevronDown className="w-5 h-5 text-gray" />
                ) : (
                    <ChevronRight className="w-5 h-5 text-gray" />
                )}
            </button>

            {/* Content Body */}
            {isOpen && (
                <div className="p-6 md:p-8 border-t border-border-gray bg-light/30">
                    <div className="space-y-8">
                        {/* 1. Global Input */}
                        <div className="flex items-center gap-4 p-4 bg-white rounded-lg border border-border-gray">
                            <div className="flex-1">
                                <p className="text-sm text-gray uppercase tracking-wide font-bold mb-1">
                                    Starting Point
                                </p>
                                <div className="flex flex-col md:flex-row items-baseline gap-2">
                                    <span className="text-2xl font-bold text-primary">
                                        {formatCurrency(result.annual_income)}
                                    </span>
                                    <span className="text-gray font-medium">
                                        Global Gross Income ({tax_year})
                                    </span>
                                </div>
                            </div>
                            <Globe className="w-8 h-8 text-gray/20" />
                        </div>

                        {/* 2. Country-by-Country Flow */}
                        <div className="space-y-4 !my-4">
                            {residency_data.map((country, index) => {
                                const taxDetails = getTaxDetails(
                                    country.country_id,
                                );
                                const isResident = country.is_tax_resident;

                                return (
                                    <div
                                        key={country.country_id}
                                        className="relative pl-0 md:pl-0"
                                    >
                                        {/* Connector Line (Desktop) */}
                                        <div className="hidden md:block absolute left-8 top-0 bottom-0 w-0.5 bg-border-gray -z-10 last:bottom-auto"></div>

                                        <div className="bg-white rounded-xl border border-border-gray shadow-sm overflow-hidden">
                                            {/* Country Header */}
                                            <div className="bg-primary/5 px-6 py-4 flex items-center justify-between border-b border-border-gray">
                                                <div className="flex items-center gap-3">
                                                    <MapPin className="w-5 h-5 text-primary" />
                                                    <h4 className="font-bold text-primary text-lg">
                                                        {country.country_name}
                                                    </h4>
                                                </div>
                                                <div
                                                    className={`px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide ${
                                                        isResident
                                                            ? "bg-primary text-light"
                                                            : "bg-gray/10 text-gray"
                                                    }`}
                                                >
                                                    {isResident
                                                        ? "Tax Resident"
                                                        : "Non-Resident"}
                                                </div>
                                            </div>

                                            <div className="p-6 space-y-6">
                                                {/* A. Residency Check */}
                                                <div className="flex items-start gap-4">
                                                    <div className="mt-1">
                                                        {isResident ? (
                                                            <CheckCircle2 className="w-5 h-5 text-primary" />
                                                        ) : (
                                                            <XCircle className="w-5 h-5 text-gray" />
                                                        )}
                                                    </div>
                                                    <div className="flex-1">
                                                        <p className="font-bold text-primary mb-1">
                                                            Residency Test
                                                        </p>
                                                        <div className="text-sm text-gray flex no-wrap md:flex-wrap gap-x-2 md:gap-x-6 gap-y-2">
                                                            <span>
                                                                Days Spent:{" "}
                                                                <strong className="text-primary">
                                                                    {
                                                                        country.days_spent
                                                                    }
                                                                </strong>
                                                            </span>
                                                            <span className="text-gray/40">
                                                                |
                                                            </span>
                                                            <span>
                                                                Threshold:{" "}
                                                                <strong>
                                                                    {
                                                                        country.threshold
                                                                    }
                                                                </strong>{" "}
                                                                days
                                                            </span>
                                                            <span className="text-gray/40">
                                                                |
                                                            </span>
                                                            <span>
                                                                Result:{" "}
                                                                <strong>
                                                                    {isResident
                                                                        ? "Passed"
                                                                        : "Failed"}
                                                                </strong>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {/* B. Tax Calculation (Residents Only) */}
                                                {isResident && taxDetails && (
                                                    <>
                                                        <div className="flex items-center justify-center">
                                                            <ArrowRight
                                                                className="w-4 h-4 text-border-gray rotate-90"
                                                                strokeWidth={2}
                                                                color="#000000"
                                                            />
                                                        </div>

                                                        {/* Income Allocation */}
                                                        <div className="bg-light/50 rounded-lg p-4 border border-border-gray">
                                                            <div className="flex justify-between items-center mb-2">
                                                                <span className="text-sm text-gray font-medium">
                                                                    Allocated
                                                                    Income
                                                                </span>
                                                                <span className="text-sm font-bold text-primary">
                                                                    {formatCurrency(
                                                                        taxDetails.allocated_income,
                                                                    )}
                                                                </span>
                                                            </div>
                                                            <div className="text-xs text-gray">
                                                                {taxDetails.tax_basis ===
                                                                    "territorial" ||
                                                                taxDetails.tax_basis ===
                                                                    "remittance" ? (
                                                                    <>
                                                                        Locally
                                                                        earned
                                                                        income
                                                                        in{" "}
                                                                        <strong>
                                                                            {
                                                                                country.country_name
                                                                            }
                                                                        </strong>
                                                                        {/* Show conversion if currencies differ */}
                                                                        {taxDetails.local_income_currency &&
                                                                            taxDetails.local_income_currency !==
                                                                                result.currency &&
                                                                            taxDetails.local_income_original !=
                                                                                null && (
                                                                                <span className="ml-1 text-gray/70">
                                                                                    (
                                                                                    {
                                                                                        taxDetails.local_income_currency
                                                                                    }{" "}
                                                                                    {Number(
                                                                                        taxDetails.local_income_original,
                                                                                    ).toLocaleString()}{" "}
                                                                                    &rarr;{" "}
                                                                                    {
                                                                                        result.currency
                                                                                    }{" "}
                                                                                    {Number(
                                                                                        taxDetails.allocated_income,
                                                                                    ).toLocaleString()}

                                                                                    )
                                                                                </span>
                                                                            )}
                                                                    </>
                                                                ) : (
                                                                    <>
                                                                        (Global
                                                                        Income
                                                                        &divide;
                                                                        365)
                                                                        &times;{" "}
                                                                        {
                                                                            country.days_spent
                                                                        }{" "}
                                                                        days
                                                                    </>
                                                                )}
                                                            </div>
                                                        </div>

                                                        <div className="flex items-center justify-center">
                                                            <ArrowRight
                                                                className="w-4 h-4 text-border-gray rotate-90"
                                                                strokeWidth={2}
                                                                color="#000000"
                                                            />
                                                        </div>

                                                        {/* Tax Adjustments (FEIE) */}
                                                        {taxDetails.feie_applied && (
                                                            <div className="bg-light/50 rounded-lg p-4 border border-border-gray mb-4">
                                                                <div className="flex justify-between items-center text-primary">
                                                                    <div className="flex items-center gap-2">
                                                                        <Minus className="w-4 h-4" />
                                                                        <span className="text-sm font-bold">
                                                                            FEIE
                                                                            Exclusion
                                                                            (US)
                                                                        </span>
                                                                    </div>
                                                                    <span className="text-sm font-bold">
                                                                        -
                                                                        {formatCurrency(
                                                                            taxDetails.feie_exclusion,
                                                                        )}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        )}

                                                        {/* Tax Items Breakdown */}
                                                        <div className="space-y-4">
                                                            {taxDetails.tax_type_breakdown.map(
                                                                (item, idx) => (
                                                                    <div
                                                                        key={
                                                                            idx
                                                                        }
                                                                        className="bg-white border text-sm border-border-gray rounded-lg overflow-hidden"
                                                                    >
                                                                        <div className="flex justify-between items-center p-3 bg-light">
                                                                            <div>
                                                                                <p className="font-bold text-primary">
                                                                                    {
                                                                                        item.name
                                                                                    }
                                                                                </p>
                                                                                <p className="text-xs text-gray">
                                                                                    {
                                                                                        item.details
                                                                                    }
                                                                                </p>
                                                                            </div>
                                                                            <span className="font-bold text-primary text-base">
                                                                                {formatCurrency(
                                                                                    item.amount,
                                                                                )}
                                                                            </span>
                                                                        </div>

                                                                        {/* Render bracket details if they exist and are populated */}
                                                                        {item.bracket_details &&
                                                                            item
                                                                                .bracket_details
                                                                                .length >
                                                                                0 && (
                                                                                <div className="border-t border-border-gray bg-white">
                                                                                    <div className="overflow-x-auto w-full">
                                                                                        <table className="w-full text-xs text-left min-w-[400px]">
                                                                                            <thead className="bg-light/50 text-gray">
                                                                                                <tr>
                                                                                                    <th className="py-2 px-3 font-medium">
                                                                                                        Bracket
                                                                                                        Range
                                                                                                    </th>
                                                                                                    <th className="py-2 px-3 font-medium text-right">
                                                                                                        Taxable
                                                                                                    </th>
                                                                                                    <th className="py-2 px-3 font-medium text-right">
                                                                                                        Rate
                                                                                                    </th>
                                                                                                    <th className="py-2 px-3 font-medium text-right">
                                                                                                        Tax
                                                                                                    </th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody className="divide-y divide-border-gray/50">
                                                                                                {item.bracket_details.map(
                                                                                                    (
                                                                                                        bracket,
                                                                                                        bIdx,
                                                                                                    ) => (
                                                                                                        <tr
                                                                                                            key={
                                                                                                                bIdx
                                                                                                            }
                                                                                                            className="hover:bg-light/30 transition-colors"
                                                                                                        >
                                                                                                            <td className="py-2 px-3 text-gray">
                                                                                                                {formatCurrency(
                                                                                                                    bracket.min_income,
                                                                                                                )}{" "}
                                                                                                                {bracket.max_income
                                                                                                                    ? ` - ${formatCurrency(bracket.max_income)}`
                                                                                                                    : "+"}
                                                                                                            </td>
                                                                                                            <td className="py-2 px-3 text-right font-medium">
                                                                                                                {formatCurrency(
                                                                                                                    bracket.taxable_amount,
                                                                                                                )}
                                                                                                            </td>
                                                                                                            <td className="py-2 px-3 text-right text-gray">
                                                                                                                {Math.round(
                                                                                                                    bracket.rate,
                                                                                                                )}

                                                                                                                %
                                                                                                            </td>
                                                                                                            <td className="py-2 px-3 text-right font-bold text-primary">
                                                                                                                {formatCurrency(
                                                                                                                    bracket.tax_applied,
                                                                                                                )}
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    ),
                                                                                                )}
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            )}
                                                                    </div>
                                                                ),
                                                            )}
                                                        </div>

                                                        <div className="h-px bg-border-gray my-2"></div>

                                                        {/* Subtotal */}
                                                        <div className="flex justify-between items-center">
                                                            <span className="text-sm font-bold text-primary">
                                                                Total Tax (
                                                                {
                                                                    country.country_code
                                                                }
                                                                )
                                                            </span>
                                                            <span className="text-lg font-bold text-primary">
                                                                {formatCurrency(
                                                                    taxDetails.tax_due,
                                                                )}
                                                            </span>
                                                        </div>
                                                    </>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>

                        {/* 3. Final Aggregation */}
                        <div className="bg-primary text-light rounded-xl p-6 md:p-8 mt-2">
                            <h4 className="text-lg font-bold mb-6 border-b border-light/20 pb-4">
                                Final Calculation
                            </h4>
                            <div className="space-y-4">
                                <div className="flex justify-between items-center opacity-80">
                                    <span className="text-sm font-medium">
                                        Gross Income
                                    </span>
                                    <span className="text-base">
                                        {formatCurrency(result.annual_income)}
                                    </span>
                                </div>
                                <div className="flex justify-between items-center opacity-80">
                                    <span className="text-sm font-medium flex items-center gap-2">
                                        <Minus className="w-4 h-4" />
                                        Total Tax Liability
                                    </span>
                                    <span className="text-base text-red-300">
                                        -{formatCurrency(total_tax)}
                                    </span>
                                </div>

                                <div className="h-px bg-light/20 my-2"></div>

                                <div className="flex justify-between items-center">
                                    <span className="text-xl font-bold">
                                        Net Income
                                    </span>
                                    <span className="text-2xl font-bold text-green-400">
                                        {formatCurrency(net_income)}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
