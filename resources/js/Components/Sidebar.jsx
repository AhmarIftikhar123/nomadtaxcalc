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
import { Link, usePage } from "@inertiajs/react";
import Tooltip from "@/Components/Ui/Tooltip";

export default function Sidebar({ user, isCollapsed, isMobileOpen }) {
    const isAuthenticated = !!user;
    const { url } = usePage();

    // All menu items — with `authOnly` flag
    const allMenuItems = [
        {
            icon: LayoutGrid,
            label: "Dashboard",
            href: route("dashboard"),
            authOnly: true,
        },
        {
            icon: FileText,
            label: "Nomad Tax Calculator",
            href: route("tax-calculator.index"),
            authOnly: false,
            activeRoutes: ["tax-calculator.index"],
        },
        {
            icon: FileText,
            label: "My Calculations",
            href: route("my-calculations.index"),
            authOnly: true,
            activeRoutes: ["my-calculations.index"],
        },
        // {
        //     icon: Globe,
        //     label: "Countries",
        //     href: "",
        //     authOnly: true,
        // },
        {
            icon: Settings,
            label: "Settings",
            href: "",
            authOnly: true,
        },
    ];

    const allBottomItems = [
        {
            icon: HelpCircle,
            label: "Help Center",
            href: "",
            authOnly: false,
        },
        {
            icon: LogOut,
            label: "Log Out",
            href: route("logout"),
            method: "post",
            authOnly: true,
        },
    ];

    // Filter items based on auth state
    const menuItems = allMenuItems.filter(
        (item) => !item.authOnly || isAuthenticated,
    );
    const bottomItems = allBottomItems.filter(
        (item) => !item.authOnly || isAuthenticated,
    );

    // Check if a menu item is active
    const isActive = (item) => {
        if (item.activeRoutes) {
            return item.activeRoutes.some((routeName) => {
                try {
                    return url.includes(route(routeName, undefined, false));
                } catch {
                    return false;
                }
            });
        }
        if (!item.href) return false;
        try {
            return url.startsWith(new URL(item.href).pathname);
        } catch {
            return url === item.href;
        }
    };

    // Logo href depends on auth
    const logoHref = isAuthenticated
        ? route("dashboard")
        : route("tax-calculator.index");

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
                    <Link href={logoHref}>
                        {isCollapsed ? (
                            <div className="w-10 h-10 rounded-lg bg-primary flex items-center justify-center text-light font-bold text-xl">
                                N
                            </div>
                        ) : (
                            <ApplicationLogo className="block h-8 w-auto fill-current text-primary" />
                        )}
                    </Link>
                </div>
            </div>

            {/* Main Menu */}
            <nav className="flex-1 py-6 px-3 space-y-1">
                {menuItems.map((item) => {
                    const Icon = item.icon;
                    const active = isActive(item);
                    return (
                        <Tooltip
                            key={item.label}
                            text={isCollapsed ? item.label : ""}
                            position="right"
                            delay={100}
                            className="w-full"
                        >
                            <Link
                                href={item.href}
                                className={`flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-colors duration-200 w-full ${isCollapsed ? "justify-center" : ""} ${
                                    active
                                        ? "bg-primary text-light active"
                                        : "text-primary hover:bg-border-gray"
                                }`}
                            >
                                <Icon
                                    className={`w-5 h-5 shrink-0 ${active ? "text-light" : "text-primary"}`}
                                />
                                {!isCollapsed && <span>{item.label}</span>}
                            </Link>
                        </Tooltip>
                    );
                })}
            </nav>

            {/* Bottom Menu */}
            {bottomItems.length > 0 && (
                <div className="border-t border-border-gray p-3 space-y-1">
                    {bottomItems.map((item) => {
                        const Icon = item.icon;
                        const isLogout = item.method === "post";
                        const active = isActive(item);

                        return (
                            <Tooltip
                                key={item.label}
                                text={isCollapsed ? item.label : ""}
                                position="right"
                                delay={100}
                                className="w-full"
                            >
                                <Link
                                    href={item.href}
                                    method={item.method}
                                    as={isLogout ? "button" : "a"}
                                    className={`flex w-full items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-colors duration-200 ${isCollapsed ? "justify-center" : ""} ${
                                        active
                                            ? "bg-primary text-light active"
                                            : "text-primary hover:bg-border-gray"
                                    }`}
                                >
                                    <Icon
                                        className={`w-5 h-5 shrink-0 ${active ? "text-light" : "text-primary"}`}
                                    />
                                    {!isCollapsed && <span>{item.label}</span>}
                                </Link>
                            </Tooltip>
                        );
                    })}
                </div>
            )}
        </aside>
    );
}
