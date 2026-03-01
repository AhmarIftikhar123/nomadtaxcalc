"use client";

import React from "react";
import { Link } from "@inertiajs/react";
import { Calculator, FileText, UserCog } from "lucide-react";

const actions = [
    {
        icon: Calculator,
        label: "New Calculation",
        description: "Start a fresh tax analysis",
        href: "tax-calculator.index",
        primary: true,
    },
    {
        icon: FileText,
        label: "My Calculations",
        description: "View all saved results",
        href: "my-calculations.index",
        primary: false,
    },
    {
        icon: UserCog,
        label: "Profile Settings",
        description: "Update your account",
        href: "profile.edit",
        primary: false,
    },
];

export default function QuickActions() {
    return (
        <div className="bg-white rounded-xl border border-border-gray p-5 shadow-sm">
            <h3 className="text-xs font-semibold text-gray uppercase tracking-wider mb-4">
                Quick Actions
            </h3>
            <div className="space-y-2.5">
                {actions.map((action) => {
                    const Icon = action.icon;
                    return (
                        <Link
                            key={action.label}
                            href={route(action.href)}
                            className={`flex items-center gap-3 px-4 py-3.5 rounded-lg text-sm font-semibold transition-all w-full ${
                                action.primary
                                    ? "bg-primary text-light hover:bg-dark"
                                    : "border border-border-gray text-primary hover:bg-light"
                            }`}
                        >
                            <Icon className="w-5 h-5 flex-shrink-0" />
                            <div className="min-w-0">
                                <span className="block leading-tight">
                                    {action.label}
                                </span>
                                <span
                                    className={`block text-xs font-normal ${action.primary ? "text-light/70" : "text-gray"}`}
                                >
                                    {action.description}
                                </span>
                            </div>
                        </Link>
                    );
                })}
            </div>
        </div>
    );
}
