import { useState } from "react";
import Sidebar from "@/Components/Sidebar";
import TopBar from "@/Components/TopBar";
import { usePage } from "@inertiajs/react";

export default function AuthenticatedLayout({
    header,
    children,
    title = "Dashboard",
    onRecalculate,
}) {
    const user = usePage().props.auth.user;
    const [isSidebarCollapsed, setIsSidebarCollapsed] = useState(false);
    const [isMobileSidebarOpen, setIsMobileSidebarOpen] = useState(false);

    const toggleSidebar = () => {
        if (window.innerWidth >= 768) {
            setIsSidebarCollapsed(!isSidebarCollapsed);
        } else {
            setIsMobileSidebarOpen(!isMobileSidebarOpen);
        }
    };

    return (
        <div className="min-h-screen bg-light font-sans text-primary">
            {/* Sidebar */}
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
                <main className="flex-1 p-6 md:p-8 overflow-y-auto">
                    {header && (
                        <header className="mb-8">
                            <div className="max-w-7xl mx-auto">{header}</div>
                        </header>
                    )}
                    {children}
                </main>
            </div>
        </div>
    );
}
