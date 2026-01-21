"use client";

import React, { useState, useMemo } from "react";
import {
    Search,
    Plus,
    CheckCircle2,
    AlertTriangle,
    Loader,
    ArrowRight,
    ChevronLeft,
} from "lucide-react";
import ResidencyPeriodItem from "./ResidencyPeriodItem";
import InputError from "@/Components/InputError";

const MONTHS = [
    "Jan",
    "Feb",
    "Mar",
    "Apr",
    "May",
    "Jun",
    "Jul",
    "Aug",
    "Sep",
    "Oct",
    "Nov",
    "Dec",
];

const COUNTRIES = [
    "United States",
    "United Kingdom",
    "Canada",
    "Australia",
    "Germany",
    "France",
    "Spain",
    "Italy",
    "Netherlands",
    "Belgium",
    "Switzerland",
    "Sweden",
    "Norway",
    "Denmark",
    "Portugal",
    "Thailand",
    "Mexico",
    "India",
    "Japan",
    "Singapore",
    "New Zealand",
    "Ireland",
    "Greece",
    "Austria",
    "Poland",
    "Czech Republic",
    "Hungary",
    "Romania",
    "Bulgaria",
];

export default function Step2Form({
    data,
    setData,
    errors,
    processing,
    onSubmit,
    onBack,
}) {
    const [searchTerm, setSearchTerm] = useState("");
    const [selectedCountry, setSelectedCountry] = useState("");
    const [daysSpent, setDaysSpent] = useState("");
    const [residencyPeriods, setResidencyPeriods] = useState(
        data.residency_periods || [],
    );

    // Filter countries
    const filteredCountries = useMemo(() => {
        return COUNTRIES.filter(
            (c) =>
                c.toLowerCase().includes(searchTerm.toLowerCase()) &&
                !residencyPeriods.some((r) => r.country === c),
        );
    }, [searchTerm, residencyPeriods]);

    // Calculate total days
    const totalDays = useMemo(() => {
        return residencyPeriods.reduce(
            (sum, period) => sum + parseInt(period.days || 0),
            0,
        );
    }, [residencyPeriods]);

    const daysRemaining = 365 - totalDays;
    const isYearComplete = totalDays === 365;
    const hasErrors = totalDays > 365;

    // Calculate progress percentage
    const progressPercentage = Math.min((totalDays / 365) * 100, 100);

    const handleAddPeriod = () => {
        if (!selectedCountry || !daysSpent || parseInt(daysSpent) <= 0) {
            return;
        }

        if (totalDays + parseInt(daysSpent) > 365) {
            return;
        }

        const newPeriod = {
            id: Date.now(),
            country: selectedCountry,
            days: parseInt(daysSpent),
            startMonth: 0,
            endMonth: 11,
            isTaxResident: parseInt(daysSpent) >= 183,
        };

        setResidencyPeriods([...residencyPeriods, newPeriod]);
        setSelectedCountry("");
        setDaysSpent("");
        setSearchTerm("");
        setData("residency_periods", [...residencyPeriods, newPeriod]);
    };

    const handleRemovePeriod = (id) => {
        const updated = residencyPeriods.filter((p) => p.id !== id);
        setResidencyPeriods(updated);
        setData("residency_periods", updated);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!isYearComplete) {
            return;
        }
        onSubmit();
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-8">
            {/* Fiscal Year Timeline */}
            <div className="bg-light rounded-xl border border-border-gray p-6 md:p-8">
                <h3 className="text-xl font-bold text-primary mb-2">
                    Fiscal Year 2024
                </h3>
                <p className="text-sm text-gray mb-6">{totalDays}/365 Days</p>

                {/* Progress Bar */}
                <div className="mb-6">
                    <div className="relative w-full h-3 bg-border-gray rounded-full overflow-hidden">
                        <div
                            className="absolute h-full bg-primary transition-all duration-300"
                            style={{ width: `${progressPercentage}%` }}
                        />
                    </div>
                    <div className="flex justify-between text-xs text-gray mt-2">
                        <span>Jan 1</span>
                        <span>Dec 31</span>
                    </div>
                </div>

                {/* Status Message */}
                {isYearComplete && (
                    <div className="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start gap-3">
                        <CheckCircle2 className="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" />
                        <p className="text-sm text-green-700">
                            Great! You have accounted for all 365 days of the
                            year.
                        </p>
                    </div>
                )}

                {!isYearComplete && daysRemaining > 0 && (
                    <div className="bg-primary bg-opacity-5 border border-primary border-opacity-20 rounded-lg p-4 flex items-start gap-3">
                        <AlertTriangle className="w-5 h-5 text-primary flex-shrink-0 mt-0.5" />
                        <p className="text-sm text-primary">
                            You have <strong>{daysRemaining} days</strong>{" "}
                            remaining to account for in 2024.
                        </p>
                    </div>
                )}

                {hasErrors && (
                    <div className="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3">
                        <AlertTriangle className="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                        <p className="text-sm text-red-700">
                            Total days exceed 365. Please adjust your entries.
                        </p>
                    </div>
                )}
            </div>

            {/* Add Residency Period */}
            <div className="bg-light rounded-xl border border-border-gray p-6 md:p-8">
                <h3 className="text-lg font-bold text-primary mb-6 uppercase text-sm tracking-wide">
                    Add Residency Period
                </h3>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    {/* Country Search */}
                    <div className="md:col-span-2">
                        <div className="relative">
                            <Search className="absolute left-3 top-3.5 w-5 h-5 text-gray pointer-events-none" />
                            <input
                                type="text"
                                placeholder="Search country..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                onFocus={() => {
                                    if (!selectedCountry) {
                                        setSearchTerm("");
                                    }
                                }}
                                className="w-full pl-10 pr-4 py-3 border border-border-gray rounded-lg text-base font-sans focus:ring-2 focus:ring-primary focus:ring-opacity-50 focus:border-transparent outline-none transition"
                            />

                            {/* Country Dropdown */}
                            {searchTerm && filteredCountries.length > 0 && (
                                <div className="absolute top-full left-0 right-0 mt-2 bg-white border border-border-gray rounded-lg shadow-lg max-h-64 overflow-y-auto z-10">
                                    {filteredCountries.map((country) => (
                                        <button
                                            key={country}
                                            type="button"
                                            onClick={() => {
                                                setSelectedCountry(country);
                                                setSearchTerm("");
                                            }}
                                            className="w-full text-left px-4 py-3 hover:bg-light transition border-b border-border-gray last:border-b-0"
                                        >
                                            <p className="font-medium text-primary">
                                                {country}
                                            </p>
                                        </button>
                                    ))}
                                </div>
                            )}
                        </div>

                        {selectedCountry && !searchTerm && (
                            <div className="mt-2 px-4 py-2 bg-primary bg-opacity-5 border border-primary border-opacity-20 rounded-lg text-sm text-primary font-medium">
                                Selected: {selectedCountry}
                            </div>
                        )}
                    </div>

                    {/* Days Spent */}
                    <div>
                        <input
                            type="number"
                            placeholder="Days spent"
                            value={daysSpent}
                            onChange={(e) => setDaysSpent(e.target.value)}
                            min="1"
                            max={daysRemaining}
                            className="w-full px-4 py-3 border border-border-gray rounded-lg text-base font-sans focus:ring-2 focus:ring-primary focus:ring-opacity-50 focus:border-transparent outline-none transition"
                        />
                    </div>
                </div>

                {/* Add Button */}
                <button
                    type="button"
                    onClick={handleAddPeriod}
                    disabled={
                        !selectedCountry ||
                        !daysSpent ||
                        parseInt(daysSpent) <= 0 ||
                        totalDays + parseInt(daysSpent) > 365
                    }
                    className="px-6 py-3 bg-light border-2 border-primary text-primary font-bold rounded-lg hover:bg-primary hover:text-light disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center gap-2 justify-center"
                >
                    <Plus className="w-5 h-5" />
                    Add
                </button>
            </div>

            {/* Residency Periods List */}
            {residencyPeriods.length > 0 && (
                <div className="space-y-3">
                    {residencyPeriods.map((period) => (
                        <ResidencyPeriodItem
                            key={period.id}
                            country={period.country}
                            days={period.days}
                            dateRange={`Jan 1 - Dec 31`}
                            isTaxResident={period.isTaxResident}
                            onRemove={() => handleRemovePeriod(period.id)}
                        />
                    ))}
                </div>
            )}

            {/* Navigation Buttons */}
            <div className="flex gap-4 pt-8 border-t border-border-gray">
                <button
                    type="button"
                    onClick={onBack}
                    className="flex-1 border-2 border-primary text-primary font-bold py-4 px-6 rounded-lg hover:bg-primary hover:text-light transition-all flex items-center justify-center gap-2"
                >
                    <ChevronLeft className="w-5 h-5" />
                    Back
                </button>
                <button
                    type="submit"
                    disabled={processing || !isYearComplete}
                    className="flex-1 bg-primary hover:bg-dark disabled:bg-gray disabled:cursor-not-allowed text-light font-bold py-4 px-6 rounded-lg transition-all flex items-center justify-center gap-2"
                >
                    {processing ? (
                        <>
                            <Loader className="w-5 h-5 animate-spin" />
                            Processing...
                        </>
                    ) : (
                        <>
                            Calculate Liability
                            <ArrowRight className="w-5 h-5" />
                        </>
                    )}
                </button>
            </div>
        </form>
    );
}
