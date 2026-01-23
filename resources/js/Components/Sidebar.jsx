"use client";

import React from "react";
import {
    LayoutGrid,
    FileText,
    Globe,
    Settings,
    HelpCircle,
    LogOut,
} from "lucide-react";
import { Link } from "@inertiajs/react";

export default function Sidebar({ user }) {
    const menuItems = [
        {
            icon: LayoutGrid,
            label: "Dashboard",
            href: route("dashboard"),
        },
        {
            icon: FileText,
            label: "My Taxes",
            href: route("tax-calculator.index"),
        },
        {
            icon: Globe,
            label: "Countries",
            href: '',
        },
        {
            icon: Settings,
            label: "Settings",
            href: '',
        },
    ];

    const bottomItems = [
        {
            icon: HelpCircle,
            label: "Help Center",
            href: '',
        },
        {
            icon: LogOut,
            label: "Log Out",
            href: route("logout"),
            method: "post",
        },
    ];

    return (
        <aside className="w-64 bg-light border-r border-border-gray flex flex-col h-screen fixed left-0 top-0">
            {/* Logo and Profile Section */}
            <div className="p-6 border-b border-border-gray">
                <div className="flex items-center gap-3 mb-6">
                    <div className="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-light font-bold">
                        {user?.name?.charAt(0).toUpperCase()}
                    </div>
                    <div className="flex-1">
                        <p className="font-semibold text-primary text-sm">
                            {user?.name}
                        </p>
                        <p className="text-xs text-gray">Plan Basic</p>
                    </div>
                </div>
            </div>

            {/* Main Menu */}
            <nav className="flex-1 overflow-y-auto py-6 px-3 space-y-1">
                {menuItems.map((item) => {
                    const Icon = item.icon;
                    return (
                        <Link
                            key={item.label}
                            href={item.href}
                            className="flex items-center gap-3 px-3 py-3 rounded-lg text-primary hover:bg-border-gray transition-colors duration-200 text-sm font-medium"
                        >
                            <Icon className="w-5 h-5 text-primary" />
                            <span>{item.label}</span>
                        </Link>
                    );
                })}
            </nav>

            {/* Bottom Menu */}
            <div className="border-t border-border-gray p-3 space-y-1">
                {bottomItems.map((item) => {
                    const Icon = item.icon;
                    const isLogout = item.method === "post";

                    return (
                        <Link
                            key={item.label}
                            href={item.href}
                            method={item.method}
                            as={isLogout ? "button" : "a"}
                            className="flex w-full items-center gap-3 px-3 py-3 rounded-lg text-primary hover:bg-border-gray transition-colors duration-200 text-sm font-medium"
                        >
                            <Icon className="w-5 h-5 text-primary" />
                            <span>{item.label}</span>
                        </Link>
                    );
                })}
            </div>
        </aside>
    );
}
