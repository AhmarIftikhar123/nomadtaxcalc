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
import ApplicationLogo from "@/Components/ApplicationLogo";
import { Link } from "@inertiajs/react";

export default function Sidebar({ user, isCollapsed, isMobileOpen }) {
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
            href: "",
        },
        {
            icon: Settings,
            label: "Settings",
            href: "",
        },
    ];

    const bottomItems = [
        {
            icon: HelpCircle,
            label: "Help Center",
            href: "",
        },
        {
            icon: LogOut,
            label: "Log Out",
            href: route("logout"),
            method: "post",
        },
    ];

    return (
        <aside
            className={`
                fixed top-0 left-0 z-50 h-screen bg-light border-r border-border-gray flex flex-col transition-all duration-300
                ${isCollapsed ? "w-20" : "w-64"}
                ${isMobileOpen ? "translate-x-0" : "-translate-x-full md:translate-x-0"}
            `}
        >
            {/* Logo Section */}
            <div
                className={`px-7 border-b border-border-gray ${isCollapsed ? "flex justify-center py-3" : "py-4"}`}
            >
                <div
                    className={`flex items-center gap-3 ${isCollapsed ? "justify-center" : ""}`}
                >
                    <Link href={route("dashboard")}>
                        {isCollapsed ? (
                            <div className="w-10 h-10 rounded-lg bg-primary flex items-center justify-center text-light font-bold text-xl">
                                N
                            </div>
                        ) : (
                            <ApplicationLogo  className="block h-8 w-auto fill-current text-primary" />
                        )}
                    </Link>
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
                            className={`flex items-center gap-3 px-3 py-3 rounded-lg text-primary hover:bg-border-gray transition-colors duration-200 text-sm font-medium ${isCollapsed ? "justify-center" : ""}`}
                            title={isCollapsed ? item.label : ""}
                        >
                            <Icon className="w-5 h-5 text-primary shrink-0" />
                            {!isCollapsed && <span>{item.label}</span>}
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
                            className={`flex w-full items-center gap-3 px-3 py-3 rounded-lg text-primary hover:bg-border-gray transition-colors duration-200 text-sm font-medium ${isCollapsed ? "justify-center" : ""}`}
                            title={isCollapsed ? item.label : ""}
                        >
                            <Icon className="w-5 h-5 text-primary shrink-0" />
                            {!isCollapsed && <span>{item.label}</span>}
                        </Link>
                    );
                })}
            </div>
        </aside>
    );
}
