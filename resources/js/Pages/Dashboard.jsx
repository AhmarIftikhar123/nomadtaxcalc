import React from "react";
import { Head, Link, usePage } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import StatCard from "@/Components/Dashboard/StatCard";
import RecentCalculationCard from "@/Components/Dashboard/RecentCalculationCard";
import QuickActions from "@/Components/Dashboard/QuickActions";
import TaxYearChart from "@/Components/Dashboard/TaxYearChart";
import TopCountries from "@/Components/Dashboard/TopCountries";
import {
    Calculator,
    BarChart3,
    Globe,
    FileText,
    ArrowRight,
} from "lucide-react";

export default function Dashboard({
    stats = {},
    recentCalculations = [],
    yearBreakdown = {},
    topCountries = [],
}) {
    const { auth } = usePage().props;
    const userName = auth?.user?.name?.split(" ")[0] || "there";
    const isEmpty = stats.totalCalculations === 0;

    return (
        <AuthenticatedLayout title="Dashboard">
            <Head title="Dashboard" />

            <div className="max-w-7xl mx-auto space-y-8">
                {/* ── Welcome Header ──────────────────────────────── */}
                <div>
                    <h1 className="text-2xl md:text-3xl font-extrabold text-primary leading-tight">
                        Welcome back, {userName} 👋
                    </h1>
                    <p className="text-sm text-gray mt-1">
                        {isEmpty
                            ? "Get started by running your first tax calculation."
                            : "Here's your tax overview at a glance."}
                    </p>
                </div>

                {isEmpty ? (
                    /* ── Empty State ──────────────────────────────── */
                    <div className="bg-white rounded-xl border border-border-gray p-12 shadow-sm text-center">
                        <div className="w-16 h-16 mx-auto mb-5 rounded-2xl bg-primary/10 flex items-center justify-center">
                            <Calculator className="w-8 h-8 text-primary" />
                        </div>
                        <h2 className="text-xl font-bold text-primary mb-2">
                            No calculations yet
                        </h2>
                        <p className="text-sm text-gray max-w-md mx-auto mb-6">
                            Start your first tax calculation to see your
                            personalized dashboard with insights, stats, and
                            recommendations.
                        </p>
                        <Link
                            href={route("tax-calculator.index")}
                            className="inline-flex items-center gap-2 px-8 py-4 bg-primary text-light font-bold rounded-lg hover:bg-dark transition-all text-sm"
                        >
                            <Calculator className="w-5 h-5" />
                            Start Calculating
                            <ArrowRight className="w-4 h-4" />
                        </Link>
                    </div>
                ) : (
                    <>
                        {/* ── Stat Cards Row ─────────────────────── */}
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <StatCard
                                icon={Calculator}
                                label="Total Calculations"
                                value={stats.totalCalculations}
                            />
                            <StatCard
                                icon={BarChart3}
                                label="Avg Effective Rate"
                                value={`${stats.avgEffectiveRate}%`}
                                accent={
                                    stats.avgEffectiveRate > 30
                                        ? "text-red-600"
                                        : stats.avgEffectiveRate < 15
                                          ? "text-green-600"
                                          : ""
                                }
                            />
                            <StatCard
                                icon={Globe}
                                label="Countries Analyzed"
                                value={stats.countriesAnalyzed}
                            />
                            <StatCard
                                icon={FileText}
                                label="Saved Calculations"
                                value={stats.savedCalculations}
                            />
                        </div>

                        {/* ── Two-Column Layout ──────────────────── */}
                        <div className="grid grid-cols-1 lg:grid-cols-5 gap-6">
                            {/* Left — Recent Calculations (3/5) */}
                            <div className="lg:col-span-3 space-y-4">
                                <h2 className="text-sm font-semibold text-gray uppercase tracking-wider">
                                    Recent Calculations
                                </h2>
                                {recentCalculations.map((calc) => (
                                    <RecentCalculationCard
                                        key={calc.id}
                                        calc={calc}
                                    />
                                ))}
                                <Link
                                    href={route("my-calculations.index")}
                                    className="inline-flex items-center gap-1.5 text-sm font-bold text-primary hover:text-dark transition-colors mt-2"
                                >
                                    View All Calculations
                                    <ArrowRight className="w-4 h-4" />
                                </Link>
                            </div>

                            {/* Right — Sidebar Widgets (2/5) */}
                            <div className="lg:col-span-2 space-y-5">
                                <QuickActions />
                                <TaxYearChart yearBreakdown={yearBreakdown} />
                                <TopCountries countries={topCountries} />
                            </div>
                        </div>
                    </>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
