import React from "react";
import Header from "@/Components/Header";
import Footer from "@/Components/Footer";

export default function LandingLayout({ children }) {
    return (
        <div className="min-h-screen flex flex-col bg-light dark:bg-dark text-primary dark:text-white transition-colors duration-300">
            <Header />
            <main className="flex-1">{children}</main>
            <Footer />
        </div>
    );
}
