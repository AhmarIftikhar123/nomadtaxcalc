import React, { useState } from "react";
import { Link } from "@inertiajs/react";
import Logo from "@/assets/images/logos/logo-desktop.png";

export default function Header() {
    const [isMenuOpen, setIsMenuOpen] = useState(false);

    return (
        <header className="sticky top-0 z-50 w-full border-b border-solid border-border-gray/50 bg-white/80 backdrop-blur-md px-6 md:px-20 lg:px-40 py-3 text-primary">
            <div className="flex items-center justify-between max-w-[1200px] mx-auto">
                {/* Logo */}
                <Link
                    href="/"
                    className="flex items-center gap-2 text-primary hover:opacity-80 transition-opacity"
                >
                    <img
                        src={Logo}
                        alt="NomadTax Logo"
                        className="h-9 w-auto"
                    />
                </Link>

                {/* Desktop Navigation */}
                <nav className="hidden md:flex items-center gap-2 lg:gap-8">
                    <Link
                        href="#features"
                        className="text-sm font-semibold text-primary hover:text-gray transition-colors"
                    >
                        Features
                    </Link>
                    <Link
                        href="#how-it-works"
                        className="text-sm font-semibold text-primary hover:text-gray transition-colors"
                    >
                        How It Works
                    </Link>
                    <Link
                        href="#destinations"
                        className="text-sm font-semibold text-primary hover:text-gray transition-colors"
                    >
                        Destinations
                    </Link>
                    <Link
                        href="#testimonials"
                        className="text-sm font-semibold text-primary hover:text-gray transition-colors"
                    >
                        Testimonials
                    </Link>
                </nav>

                {/* CTA Buttons */}
                <div className="flex items-center gap-4">
                    <Link href={route('tax-calculator.index')} className="flex min-w-[120px] cursor-pointer items-center justify-center rounded-xl h-10 px-5 bg-primary text-white text-sm font-bold tracking-tight hover:bg-primary/90 transition-all">
                        Try Calculator
                    </Link>
                </div>

                {/* Mobile Menu Toggle */}
                <button
                    onClick={() => setIsMenuOpen(!isMenuOpen)}
                    className="md:hidden text-primary"
                >
                    <svg
                        className="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                </button>
            </div>

            {/* Mobile Navigation */}
            {isMenuOpen && (
                <nav className="md:hidden mt-4 flex flex-col gap-4 border-t border-structure pt-4">
                    <Link
                        href="#features"
                        className="text-sm font-semibold text-text-DEFAULT hover:text-text-subtle transition-colors"
                    >
                        Features
                    </Link>
                    <Link
                        href="#how-it-works"
                        className="text-sm font-semibold text-text-DEFAULT hover:text-text-subtle transition-colors"
                    >
                        How It Works
                    </Link>
                    <Link
                        href="#destinations"
                        className="text-sm font-semibold text-text-DEFAULT hover:text-text-subtle transition-colors"
                    >
                        Destinations
                    </Link>
                    <Link
                        href="#testimonials"
                        className="text-sm font-semibold text-text-DEFAULT hover:text-text-subtle transition-colors"
                    >
                        Testimonials
                    </Link>
                </nav>
            )}
        </header>
    );
}
