import React from "react";
import { Link, router } from "@inertiajs/react";
import { Calculator, Trash2, Eye, TrendingDown, Calendar } from "lucide-react";
import TaxCalculatorLayout from "@/Layouts/TaxCalculatorLayout";

export default function MyCalculationsIndex({ calculations = [] }) {
    const handleDelete = (id) => {
        if (!confirm("Are you sure you want to delete this calculation?"))
            return;
        router.delete(route("my-calculations.destroy", id), {
            preserveScroll: true,
        });
    };

    const fmt = (amount, currency = "USD") =>
        new Intl.NumberFormat("en-US", {
            style: "currency",
            currency,
            maximumFractionDigits: 0,
        }).format(amount ?? 0);

    return (
        <TaxCalculatorLayout title="My Calculations">
            <div className="max-w-5xl mx-auto px-6 md:px-8 py-12">
                {/* Header */}
                <div className="mb-10">
                    <h1 className="text-4xl md:text-5xl font-bold text-primary mb-2">
                        My Calculations
                    </h1>
                    <p className="text-lg text-gray">
                        Your saved tax calculations. Click{" "}
                        <strong>View / Edit</strong> to load a calculation back
                        into the calculator.
                    </p>
                </div>

                {calculations.length === 0 ? (
                    /* Empty State */
                    <div className="bg-white rounded-xl border border-border-gray p-12 text-center shadow-sm">
                        <Calculator className="w-16 h-16 text-gray mx-auto mb-6 opacity-40" />
                        <h2 className="text-2xl font-bold text-primary mb-2">
                            No saved calculations yet
                        </h2>
                        <p className="text-gray mb-8">
                            Run a calculation and click{" "}
                            <strong>Save Calculation</strong> to store it here.
                        </p>
                        <Link
                            href={route("tax-calculator.index")}
                            className="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-dark text-light font-bold rounded-lg transition-all"
                        >
                            <Calculator className="w-5 h-5" />
                            Start a New Calculation
                        </Link>
                    </div>
                ) : (
                    <div className="space-y-4">
                        {calculations.map((calc) => (
                            <div
                                key={calc.id}
                                className="bg-white rounded-xl border border-border-gray p-6 shadow-sm hover:border-primary hover:border-opacity-50 transition"
                            >
                                <div className="flex items-center justify-between flex-wrap gap-4">
                                    {/* Left — Identifiers */}
                                    <div className="flex items-center gap-4">
                                        <img
                                            src={`https://flagcdn.com/w80/${(calc.citizenship_country_code ?? "us").toLowerCase()}.png`}
                                            alt={calc.citizenship_country_name}
                                            className="w-10 h-10 rounded-full object-cover"
                                            onError={(e) =>
                                                (e.target.style.display =
                                                    "none")
                                            }
                                        />
                                        <div>
                                            <p className="font-bold text-primary text-lg">
                                                {calc.citizenship_country_name}
                                            </p>
                                            <div className="flex items-center gap-3 text-sm text-gray mt-0.5">
                                                <span className="flex items-center gap-1">
                                                    <Calendar className="w-3.5 h-3.5" />
                                                    {calc.tax_year}
                                                </span>
                                                <span>·</span>
                                                <span>{calc.currency}</span>
                                                <span>·</span>
                                                <span>
                                                    Saved {calc.saved_at}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Centre — Numbers */}
                                    <div className="flex items-center gap-8">
                                        <div className="text-center">
                                            <p className="text-xs text-gray uppercase font-semibold mb-0.5">
                                                Gross Income
                                            </p>
                                            <p className="font-bold text-primary">
                                                {fmt(
                                                    calc.gross_income,
                                                    calc.currency,
                                                )}
                                            </p>
                                        </div>
                                        <div className="text-center">
                                            <p className="text-xs text-gray uppercase font-semibold mb-0.5">
                                                Total Tax
                                            </p>
                                            <p className="font-bold text-red-600">
                                                {fmt(
                                                    calc.total_tax,
                                                    calc.currency,
                                                )}
                                            </p>
                                        </div>
                                        <div className="text-center">
                                            <p className="text-xs text-gray uppercase font-semibold mb-0.5">
                                                Net Income
                                            </p>
                                            <p className="font-bold text-green-700">
                                                {fmt(
                                                    calc.net_income,
                                                    calc.currency,
                                                )}
                                            </p>
                                        </div>
                                        <div className="text-center">
                                            <p className="text-xs text-gray uppercase font-semibold mb-0.5">
                                                Eff. Rate
                                            </p>
                                            <p className="font-bold text-primary flex items-center gap-1">
                                                <TrendingDown className="w-4 h-4 text-red-500" />
                                                {(
                                                    calc.effective_tax_rate ?? 0
                                                ).toFixed(1)}
                                                %
                                            </p>
                                        </div>
                                    </div>

                                    {/* Right — Actions */}
                                    <div className="flex items-center gap-3">
                                        <Link
                                            href={route(
                                                "tax-calculator.index",
                                                {
                                                    calculation_id: calc.id,
                                                },
                                            )}
                                            className="flex items-center gap-2 px-5 py-2.5 border-2 border-primary text-primary font-bold rounded-lg hover:bg-primary hover:text-light transition-all text-sm"
                                        >
                                            <Eye className="w-4 h-4" />
                                            View / Edit
                                        </Link>
                                        <button
                                            onClick={() =>
                                                handleDelete(calc.id)
                                            }
                                            className="flex items-center gap-2 px-5 py-2.5 border-2 border-red-300 text-red-600 font-bold rounded-lg hover:bg-red-600 hover:text-white hover:border-red-600 transition-all text-sm"
                                        >
                                            <Trash2 className="w-4 h-4" />
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        ))}

                        {/* Start New */}
                        <div className="pt-4 text-center">
                            <Link
                                href={route("tax-calculator.index")}
                                className="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-dark text-light font-bold rounded-lg transition-all"
                            >
                                <Calculator className="w-5 h-5" />
                                New Calculation
                            </Link>
                        </div>
                    </div>
                )}
            </div>
        </TaxCalculatorLayout>
    );
}
