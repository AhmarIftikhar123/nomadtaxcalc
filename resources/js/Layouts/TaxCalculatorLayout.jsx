"use client";

import { useState, useEffect } from "react";
import Sidebar from "@/Components/Sidebar";
import TopBar from "@/Components/TopBar";
import Footer from "@/Components/Footer";
import { usePage } from "@inertiajs/react";

export default function TaxCalculatorLayout({
    children,
    title = "Tax Calculator",
    onRecalculate,
}) {
    const { auth } = usePage().props;
    const user = auth?.user;
    const isAuthenticated = !!user;

    // Initialize from localStorage safely (after mount to avoid hydration mismatch)
    const [isSidebarCollapsed, setIsSidebarCollapsed] = useState(false);
    const [isMobileSidebarOpen, setIsMobileSidebarOpen] = useState(false);

    useEffect(() => {
        const stored = localStorage.getItem("sidebarCollapsed");
        if (stored) {
            setIsSidebarCollapsed(JSON.parse(stored));
        }
    }, []);

    const toggleSidebar = () => {
        if (window.innerWidth >= 768) {
            const newState = !isSidebarCollapsed;
            setIsSidebarCollapsed(newState);
            localStorage.setItem("sidebarCollapsed", JSON.stringify(newState));
        } else {
            setIsMobileSidebarOpen(!isMobileSidebarOpen);
        }
    };

    return (
        <div className="min-h-screen bg-light font-sans text-primary">
            {/* Sidebar — always visible, items controlled by auth state */}
            <Sidebar
                user={user}
                isCollapsed={isSidebarCollapsed}
                isMobileOpen={isMobileSidebarOpen}
            />

            {/* Mobile Overlay */}
            {isMobileSidebarOpen && (
                <div
                    className="fixed inset-0 bg-black/50 z-40 md:hidden"
                    onClick={() => setIsMobileSidebarOpen(false)}
                />
            )}

            {/* Main Content Area */}
            <div
                className={`flex-1 flex flex-col min-h-screen transition-all duration-300 ${
                    isSidebarCollapsed ? "md:ml-20" : "md:ml-64"
                }`}
            >
                {/* TopBar */}
                <TopBar
                    title={title}
                    user={user}
                    onRecalculate={onRecalculate}
                    onToggleSidebar={toggleSidebar}
                />

                {/* Page Content */}
                <main className="flex-1 p-2 md:p-8 overflow-y-auto">
                    {children}
                </main>

                {/* Footer — only for guests */}
                {!isAuthenticated && <Footer />}
            </div>
        </div>
    );
}
