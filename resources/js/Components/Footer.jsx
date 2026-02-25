import React from "react";
import Logo from "@/assets/images/logos/logo-desktop.png";
import { useForm } from "@inertiajs/react";

export default function Footer() {
    const AppName = import.meta.env.VITE_APP_NAME;
    const {
        data,
        setData,
        post,
        processing,
        errors,
        reset,
        recentlySuccessful,
    } = useForm({
        email: "",
    });

    const handleSubscribe = (e) => {
        e.preventDefault();
        post(route("newsletter.subscribe"), {
            preserveScroll: true,
            onSuccess: () => reset("email"),
        });
    };

    return (
        <footer className="bg-dark text-white px-6 pt-20 pb-10 lg:px-40">
            <div className="max-w-[1200px] mx-auto">
                {/* Newsletter CTA */}
                <div className="flex flex-col lg:flex-row items-center justify-between pb-16 border-b border-white/10 mb-16 gap-8">
                    <div>
                        <h3 className="text-2xl font-bold mb-2">
                            Stay tax compliant, worldwide.
                        </h3>
                        <p className="text-gray-400">
                            Get monthly tax updates and visa news directly in
                            your inbox.
                        </p>
                    </div>
                    <form
                        onSubmit={handleSubscribe}
                        className="flex flex-col md:flex-row w-full lg:w-auto gap-3 items-start md:items-center relative"
                    >
                        <div className="flex flex-col w-full lg:w-auto">
                            <input
                                className="flex-1 lg:w-64 bg-white/10 border-white/20 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-white/30 transition-all outline-none border-none disabled:opacity-50"
                                placeholder="Enter your email"
                                type="email"
                                value={data.email}
                                onChange={(e) =>
                                    setData("email", e.target.value)
                                }
                                required
                                disabled={processing}
                            />
                            {errors.email && (
                                <span className="text-red-400 text-xs mt-1 absolute md:top-12 md:left-0">
                                    {errors.email}
                                </span>
                            )}
                            {recentlySuccessful && (
                                <span className="text-green-400 text-xs mt-1 absolute md:top-12 md:left-0">
                                    Successfully subscribed!
                                </span>
                            )}
                        </div>
                        <button
                            type="submit"
                            disabled={processing}
                            className="bg-white text-primary px-6 py-2 rounded-lg font-bold text-sm hover:bg-gray-200 transition-colors disabled:opacity-50 h-[44px] flex items-center justify-center whitespace-nowrap"
                        >
                            {processing ? "Joining..." : "Join Newsletter"}
                        </button>
                    </form>
                </div>

                {/* Footer Links */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-12 mb-20">
                    <div className="col-span-2 md:col-span-1">
                        <div className="flex items-center gap-2 mb-6">
                            <img
                                src={Logo}
                                alt="NomadTax Logo"
                                className="h-9 w-auto brightness-0 invert"
                            />
                        </div>
                        <p className="text-gray-400 text-sm leading-relaxed mb-6">
                            Empowering digital nomads with clarity and
                            confidence in their global tax strategy.
                        </p>
                        <div className="flex gap-4">
                            <a
                                className="text-gray-400 hover:text-white transition-colors"
                                href="#"
                            >
                                <svg
                                    className="w-5 h-5"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z" />
                                </svg>
                            </a>
                            <a
                                className="text-gray-400 hover:text-white transition-colors"
                                href="#"
                            >
                                <svg
                                    className="w-5 h-5"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M22.46 6c-.85.38-1.76.64-2.72.75 1-.6 1.76-1.53 2.12-2.66-.93.55-1.95.95-3.04 1.17-.88-.93-2.13-1.51-3.51-1.51-2.66 0-4.81 2.15-4.81 4.81 0 .38.04.75.13 1.1-4-.2-7.53-2.11-9.9-5.02-.42.72-.66 1.55-.66 2.44 0 1.67.85 3.14 2.14 4-.79-.02-1.53-.24-2.18-.6v.06c0 2.33 1.66 4.28 3.86 4.72-.4.11-.83.17-1.27.17-.31 0-.62-.03-.92-.08.62 1.93 2.42 3.34 4.55 3.38-1.67 1.31-3.77 2.09-6.05 2.09-.39 0-.78-.02-1.17-.07 2.18 1.4 4.77 2.21 7.55 2.21 9.06 0 14.01-7.5 14.01-14.01 0-.21 0-.42-.02-.63.96-.7 1.79-1.56 2.45-2.55z" />
                                </svg>
                            </a>
                            <a
                                className="text-gray-400 hover:text-white transition-colors"
                                href="#"
                            >
                                <svg
                                    className="w-5 h-5"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h4 className="font-bold mb-6 text-white uppercase text-xs tracking-widest">
                            PRODUCT
                        </h4>
                        <ul className="space-y-4 text-gray-400 text-sm">
                            <li>
                                <a className="hover:text-white" href="#">
                                    Tax Calculator
                                </a>
                            </li>
                            <li>
                                <a className="hover:text-white" href="#">
                                    Visa Database
                                </a>
                            </li>
                            <li>
                                <a className="hover:text-white" href="#">
                                    Country Reports
                                </a>
                            </li>
                            <li>
                                <a className="hover:text-white" href="#">
                                    API for Platforms
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h4 className="font-bold mb-6 text-white uppercase text-xs tracking-widest">
                            RESOURCES
                        </h4>
                        <ul className="space-y-4 text-gray-400 text-sm">
                            <li>
                                <a className="hover:text-white" href="#">
                                    Nomad Guides
                                </a>
                            </li>
                            <li>
                                <a className="hover:text-white" href="#">
                                    Tax Glossary
                                </a>
                            </li>
                            <li>
                                <a className="hover:text-white" href="#">
                                    Case Studies
                                </a>
                            </li>
                            <li>
                                <a className="hover:text-white" href="#">
                                    Support Center
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h4 className="font-bold mb-6 text-white uppercase text-xs tracking-widest">
                            COMPANY
                        </h4>
                        <ul className="space-y-4 text-gray-400 text-sm">
                            <li>
                                <a className="hover:text-white" href="#">
                                    About Us
                                </a>
                            </li>
                            <li>
                                <a className="hover:text-white" href="#">
                                    Careers
                                </a>
                            </li>
                            <li>
                                <a className="hover:text-white" href="#">
                                    Privacy Policy
                                </a>
                            </li>
                            <li>
                                <a className="hover:text-white" href="#">
                                    Terms of Service
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                {/* Copyright */}
                <div className="text-center text-gray-500 text-xs border-t border-white/5 pt-8">
                    &copy; {new Date().getFullYear()} {AppName} . All tax data
                    provided is for informational purposes only. Consult a
                    certified professional.
                </div>
            </div>
        </footer>
    );
}
