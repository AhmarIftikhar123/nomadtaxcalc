import React, { useState, useCallback } from "react";
import { Link, router, usePage } from "@inertiajs/react";
import {
    Calculator,
    Trash2,
    Eye,
    TrendingDown,
    TrendingUp,
    Search,
    SlidersHorizontal,
    ChevronLeft,
    ChevronRight,
    Plus,
} from "lucide-react";
import TaxCalculatorLayout from "@/Layouts/TaxCalculatorLayout";
import ConfirmDialog from "@/Components/Ui/ConfirmDialog";

// ─── helpers ──────────────────────────────────────────────────────────────────

const fmt = (amount, currency = "USD") =>
    new Intl.NumberFormat("en-US", {
        style: "currency",
        currency,
        maximumFractionDigits: 0,
    }).format(amount ?? 0);

const RateBadge = ({ rate }) => {
    const val = parseFloat(rate);
    const isHigh = val > 30;
    return (
        <span
            className={`inline-flex items-center gap-1 font-semibold ${
                isHigh ? "text-red-600" : "text-green-700"
            }`}
        >
            {isHigh ? (
                <TrendingUp className="w-3.5 h-3.5 shrink-0" />
            ) : (
                <TrendingDown className="w-3.5 h-3.5 shrink-0" />
            )}
            {isNaN(val) ? "N/A" : `${val.toFixed(2)}%`}
        </span>
    );
};

// ─── component ────────────────────────────────────────────────────────────────

export default function MyCalculationsIndex({
    calculations = { data: [], meta: {}, links: {} },
    filters = { search: "" },
}) {
    // Server-provided current filters
    const [search, setSearch] = useState(filters.search ?? "");

    // ConfirmDialog state
    const [confirmOpen, setConfirmOpen] = useState(false);
    const [pendingDeleteId, setPendingDeleteId] = useState(null);

    // ── debounced server search ──
    const debounceRef = React.useRef(null);

    const handleSearch = useCallback((e) => {
        const val = e.target.value;
        setSearch(val);
        clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => {
            router.get(
                route("my-calculations.index"),
                { search: val || undefined },
                { preserveState: true, replace: true },
            );
        }, 350);
    }, []);

    // ── delete ──
    const requestDelete = (id) => {
        setPendingDeleteId(id);
        setConfirmOpen(true);
    };

    const confirmDelete = () => {
        if (!pendingDeleteId) return;
        router.delete(route("my-calculations.destroy", pendingDeleteId), {
            preserveScroll: true,
        });
        setPendingDeleteId(null);
    };

    // ── pagination helpers ──
    const { data: rows = [], meta = {}, links = {} } = calculations;
    const hasPages = (meta.last_page ?? 1) > 1;

    // ── render ──
    return (
        <TaxCalculatorLayout title="My Calculations">
            {/* Confirm delete dialog */}
            <ConfirmDialog
                isOpen={confirmOpen}
                onClose={() => setConfirmOpen(false)}
                onConfirm={confirmDelete}
                title="Delete Calculation"
                message="This saved calculation will be permanently removed. This action cannot be undone."
                confirmLabel="Yes, delete"
                variant="danger"
            />

            <div className="max-w-6xl mx-auto px-4 sm:px-6 md:px-8 py-8 md:py-12">
                {/* ── Page Header ── */}
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                    <h1 className="text-3xl md:text-4xl font-bold text-primary">
                        My Calculations
                    </h1>
                    <Link
                        href={route("tax-calculator.index")}
                        className="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-dark text-light font-semibold rounded-lg transition-all text-sm shrink-0"
                    >
                        <Plus className="w-4 h-4" />
                        New Calculation
                    </Link>
                </div>

                {/* ── Search Bar (always visible) ── */}
                <div className="flex items-center gap-3 mb-6">
                    <div className="relative flex-1">
                        <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray pointer-events-none" />
                        <input
                            type="text"
                            value={search}
                            onChange={handleSearch}
                            placeholder="Search by country, year or currency…"
                            className="w-full pl-10 pr-4 py-2.5 border border-border-gray rounded-lg text-sm text-primary placeholder:text-gray bg-white focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-colors"
                        />
                    </div>
                    <button
                        className="flex items-center justify-center w-10 h-10 border border-border-gray rounded-lg hover:border-primary hover:bg-light transition-colors shrink-0"
                        title="Filter options"
                    >
                        <SlidersHorizontal className="w-4 h-4 text-gray" />
                    </button>
                </div>

                {rows.length === 0 && !search ? (
                    /* ── Empty State (no data at all) ── */
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
                    /* ── Table ── */
                    <div className="bg-white rounded-xl border border-border-gray shadow-sm overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm min-w-[680px]">
                                <thead>
                                    <tr className="border-b border-border-gray">
                                        {[
                                            "Country",
                                            "Year & Currency",
                                            "Gross Income",
                                            "Total Tax",
                                            "Net Income",
                                            "Eff. Rate",
                                            "Actions",
                                        ].map((h) => (
                                            <th
                                                key={h}
                                                className="px-5 py-3.5 text-left font-semibold text-gray uppercase tracking-wide text-xs whitespace-nowrap"
                                            >
                                                {h}
                                            </th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border-gray">
                                    {rows.length === 0 ? (
                                        <tr>
                                            <td
                                                colSpan={7}
                                                className="px-5 py-10 text-center text-gray"
                                            >
                                                No results match your search.
                                            </td>
                                        </tr>
                                    ) : (
                                        rows.map((calc) => (
                                            <tr
                                                key={calc.id}
                                                className="hover:bg-light transition-colors"
                                            >
                                                {/* Country */}
                                                <td className="px-5 py-4 whitespace-nowrap">
                                                    <div className="flex items-center gap-3">
                                                        <img
                                                            src={`https://flagcdn.com/w40/${(calc.citizenship_country_code ?? "us").toLowerCase()}.png`}
                                                            alt={
                                                                calc.citizenship_country_name
                                                            }
                                                            className="w-7 h-7 rounded-full object-cover shrink-0 border border-border-gray"
                                                            onError={(e) =>
                                                                (e.target.style.display =
                                                                    "none")
                                                            }
                                                        />
                                                        <span className="font-medium text-primary">
                                                            {
                                                                calc.citizenship_country_name
                                                            }
                                                        </span>
                                                    </div>
                                                </td>

                                                {/* Year & Currency */}
                                                <td className="px-5 py-4 text-primary whitespace-nowrap">
                                                    {calc.tax_year}
                                                    <span className="mx-1 text-gray">
                                                        •
                                                    </span>
                                                    {calc.currency}
                                                </td>

                                                {/* Gross Income */}
                                                <td className="px-5 py-4 font-medium text-primary whitespace-nowrap">
                                                    {fmt(
                                                        calc.gross_income,
                                                        calc.currency,
                                                    )}
                                                </td>

                                                {/* Total Tax */}
                                                <td className="px-5 py-4 font-medium text-primary whitespace-nowrap">
                                                    {fmt(
                                                        calc.total_tax,
                                                        calc.currency,
                                                    )}
                                                </td>

                                                {/* Net Income */}
                                                <td className="px-5 py-4 font-medium text-primary whitespace-nowrap">
                                                    {fmt(
                                                        calc.net_income,
                                                        calc.currency,
                                                    )}
                                                </td>

                                                {/* Eff. Rate */}
                                                <td className="px-5 py-4 whitespace-nowrap">
                                                    <RateBadge
                                                        rate={
                                                            calc.effective_tax_rate
                                                        }
                                                    />
                                                </td>

                                                {/* Actions */}
                                                <td className="px-5 py-4 whitespace-nowrap">
                                                    <div className="flex items-center gap-2">
                                                        <Link
                                                            href={route(
                                                                "tax-calculator.index",
                                                                {
                                                                    calculation_id:
                                                                        calc.id,
                                                                },
                                                            )}
                                                            className="p-2 rounded-lg border border-border-gray text-gray hover:border-primary hover:text-primary transition-colors"
                                                            title="View / Edit"
                                                        >
                                                            <Eye className="w-4 h-4" />
                                                        </Link>
                                                        <button
                                                            onClick={() =>
                                                                requestDelete(
                                                                    calc.id,
                                                                )
                                                            }
                                                            className="p-2 rounded-lg border border-border-gray text-gray hover:border-red-400 hover:text-red-600 transition-colors"
                                                            title="Delete"
                                                        >
                                                            <Trash2 className="w-4 h-4" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* ── Pagination ── */}
                        {hasPages && (
                            <div className="flex items-center justify-between gap-4 px-5 py-3.5 border-t border-border-gray bg-light">
                                <p className="text-xs text-gray">
                                    Showing{" "}
                                    <span className="font-medium text-primary">
                                        {meta.from}–{meta.to}
                                    </span>{" "}
                                    of{" "}
                                    <span className="font-medium text-primary">
                                        {meta.total}
                                    </span>{" "}
                                    results
                                </p>
                                <div className="flex items-center gap-2">
                                    {links.prev ? (
                                        <Link
                                            href={links.prev}
                                            className="flex items-center gap-1 px-3 py-1.5 text-xs font-medium border border-border-gray rounded-lg text-primary hover:bg-white transition-colors"
                                            preserveState
                                        >
                                            <ChevronLeft className="w-3.5 h-3.5" />
                                            Prev
                                        </Link>
                                    ) : (
                                        <span className="flex items-center gap-1 px-3 py-1.5 text-xs font-medium border border-border-gray rounded-lg text-gray opacity-40 cursor-not-allowed">
                                            <ChevronLeft className="w-3.5 h-3.5" />
                                            Prev
                                        </span>
                                    )}
                                    <span className="text-xs text-gray px-1">
                                        Page {meta.current_page} of{" "}
                                        {meta.last_page}
                                    </span>
                                    {links.next ? (
                                        <Link
                                            href={links.next}
                                            className="flex items-center gap-1 px-3 py-1.5 text-xs font-medium border border-border-gray rounded-lg text-primary hover:bg-white transition-colors"
                                            preserveState
                                        >
                                            Next
                                            <ChevronRight className="w-3.5 h-3.5" />
                                        </Link>
                                    ) : (
                                        <span className="flex items-center gap-1 px-3 py-1.5 text-xs font-medium border border-border-gray rounded-lg text-gray opacity-40 cursor-not-allowed">
                                            Next
                                            <ChevronRight className="w-3.5 h-3.5" />
                                        </span>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>
                )}
            </div>
        </TaxCalculatorLayout>
    );
}
