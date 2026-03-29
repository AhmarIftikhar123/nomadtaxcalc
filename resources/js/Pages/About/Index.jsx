import React from "react";
import { Head } from "@inertiajs/react";
import TaxCalculatorLayout from "@/Layouts/TaxCalculatorLayout";
import DisclaimerBanner from "@/Components/Ui/DisclaimerBanner";
import { Linkedin, Github, Mail } from "lucide-react";
import CreatorAvatar from "@/assets/images/avatars/creator.png";

export default function About() {
    return (
        <TaxCalculatorLayout title="About Us">
            <Head>
                <title>About Us — Nomad Tax Calculator</title>
                <meta
                    name="description"
                    content="Learn more about why we built Nomad Tax Calculator for digital nomads and US expats."
                />
            </Head>

            {/* ── Hero Section ── */}
            <div className="bg-light pt-20 pb-16 px-6 md:px-20 lg:px-40 text-center border-b border-border-gray">
                <div className="max-w-4xl mx-auto">
                    <h1 className="text-4xl md:text-6xl font-black text-primary tracking-tight leading-tight mb-6 font-sans">
                        We built the tax calculator we couldn't find.
                    </h1>
                    <p className="text-lg md:text-xl text-gray max-w-2xl mx-auto leading-relaxed">
                        Navigating taxes as a digital nomad is a nightmare. We
                        decided to build a simple, honest tool to help you
                        figure it out.
                    </p>
                </div>
            </div>

            {/* ── Main Content ── */}
            <div className="px-6 md:px-20 lg:px-40 py-16 md:py-24 bg-white">
                <div className="max-w-4xl mx-auto space-y-20 md:space-y-32">
                    {/* ── The Problem ── */}
                    <div className="space-y-6">
                        <h2 className="text-2xl md:text-3xl font-bold text-primary border-b border-border-gray pb-4">
                            The Problem
                        </h2>
                        <div className="text-primary text-base md:text-lg leading-relaxed space-y-6 font-display">
                            <p>
                                Most tax calculators are built for people who
                                live and work in one place. If you're a digital
                                nomad, your situation is different. You might be
                                dealing with the Foreign Earned Income Exclusion
                                (FEIE), tax treaties, or simply trying to
                                understand your obligations across multiple
                                jurisdictions.
                            </p>
                            <p>
                                We got tired of piecing together spreadsheets
                                and reading dense IRS documents just to get a
                                ballpark figure of what we owed. Existing tools
                                were either too simple (ignoring nomad-specific
                                rules) or too complex (requiring a CPA to
                                interpret).
                            </p>
                            <p>
                                So, we built Nomad Tax Calculator. It's designed
                                specifically for US expats and digital nomads
                                who need clear, actionable estimates.
                            </p>
                        </div>
                    </div>

                    {/* ── What Makes Us Different ── */}
                    <div className="space-y-10">
                        <h2 className="text-2xl md:text-3xl font-bold text-center text-primary">
                            What Makes Us Different
                        </h2>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {/* Card 1 */}
                            <div className="bg-light p-8 rounded-xl border border-border-gray shadow-sm hover:shadow-md transition-shadow">
                                <div className="w-10 h-10 bg-white rounded-lg flex items-center justify-center border border-border-gray mb-6 shadow-sm">
                                    <svg
                                        className="w-5 h-5 text-primary"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                        ></path>
                                    </svg>
                                </div>
                                <h3 className="text-xl font-bold text-primary mb-3">
                                    Progressive Brackets
                                </h3>
                                <p className="text-gray text-sm leading-relaxed">
                                    Accurately calculates standard deduction and
                                    progressive tax brackets rather than
                                    applying a flat rate.
                                </p>
                            </div>

                            {/* Card 2 */}
                            <div className="bg-light p-8 rounded-xl border border-border-gray shadow-sm hover:shadow-md transition-shadow">
                                <div className="w-10 h-10 bg-white rounded-lg flex items-center justify-center border border-border-gray mb-6 shadow-sm">
                                    <svg
                                        className="w-5 h-5 text-primary"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth="2"
                                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        ></path>
                                    </svg>
                                </div>
                                <h3 className="text-xl font-bold text-primary mb-3">
                                    FEIE Support
                                </h3>
                                <p className="text-gray text-sm leading-relaxed">
                                    Built-in support for the Foreign Earned
                                    Income Exclusion, easily toggle it to see
                                    your savings.
                                </p>
                            </div>

                            {/* Card 3 */}
                            <div className="bg-light p-8 rounded-xl border border-border-gray shadow-sm hover:shadow-md transition-shadow">
                                <div className="w-10 h-10 bg-white rounded-lg flex items-center justify-center border border-border-gray mb-6 shadow-sm">
                                    <svg
                                        className="w-5 h-5 text-primary"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        ></path>
                                    </svg>
                                </div>
                                <h3 className="text-xl font-bold text-primary mb-3">
                                    Self-Employment Taxes
                                </h3>
                                <p className="text-gray text-sm leading-relaxed">
                                    Clearly breaks down the self-employment tax
                                    (FICA) which often catches nomads by
                                    surprise.
                                </p>
                            </div>

                            {/* Card 4 */}
                            <div className="bg-light p-8 rounded-xl border border-border-gray shadow-sm hover:shadow-md transition-shadow">
                                <div className="w-10 h-10 bg-white rounded-lg flex items-center justify-center border border-border-gray mb-6 shadow-sm">
                                    <svg
                                        className="w-5 h-5 text-primary"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        ></path>
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        ></path>
                                    </svg>
                                </div>
                                <h3 className="text-xl font-bold text-primary mb-3">
                                    Transparent Math
                                </h3>
                                <p className="text-gray text-sm leading-relaxed">
                                    We show you exactly how we arrived at the
                                    number, step-by-step, so you can trust the
                                    estimate.
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* ── Honesty & Funding ── */}
                    <div className="border-t border-border-gray pt-10">
                        <div className="flex flex-col sm:flex-row gap-4 items-start max-w-2xl mx-auto">
                            <div className="flex-shrink-0 mt-1">
                                <svg
                                    className="w-6 h-6 text-yellow-500"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fillRule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clipRule="evenodd"
                                    ></path>
                                </svg>
                            </div>
                            <div>
                                <h4 className="text-lg font-bold text-primary mb-2">
                                    A Note on Honesty & Funding
                                </h4>
                                <p className="text-gray text-sm leading-relaxed">
                                    This tool is free to use. We don't sell your
                                    data, and we don't try to upsell you on
                                    expensive CPA services. We fund the
                                    development and hosting of this site through
                                    unobtrusive Google AdSense ads. We believe
                                    in keeping essential tools accessible to
                                    everyone.
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* ── The Builder ── */}
                    <div className="space-y-10">
                        <h2 className="text-2xl md:text-3xl font-black font-sans text-center text-primary tracking-tight">
                            The Builder
                        </h2>

                        <div className="max-w-md mx-auto bg-light border border-border-gray rounded-2xl p-8 text-center shadow-sm">
                            <div className="w-24 h-24 mx-auto rounded-full mb-6 overflow-hidden flex items-center justify-center bg-primary">
                                <img
                                    src={CreatorAvatar}
                                    alt="Ahmar Iftikhar"
                                    className="w-full h-full object-cover"
                                    onError={(e) => {
                                        e.target.style.display = "none";
                                        e.target.nextSibling.style.display =
                                            "block";
                                    }}
                                />
                                <svg
                                    className="w-12 h-12 text-light/50 hidden"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fillRule="evenodd"
                                        d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clipRule="evenodd"
                                    ></path>
                                </svg>
                            </div>

                            <h3 className="text-2xl font-bold text-primary mb-1">
                                Ahmar Iftikhar
                            </h3>
                            <p className="text-gray text-xs uppercase tracking-wider font-bold mb-5">
                                Full-Stack Developer & Open Source Builder
                            </p>

                            <p className="text-gray text-sm leading-relaxed mb-8">
                                Based in Pakistan, building full-stack web apps
                                with Laravel, React & WordPress. Started coding
                                professionally in 2022 and hasn't stopped
                                shipping since — from humanitarian platforms to
                                SaaS tools.
                            </p>

                            {/* Skills */}
                            <div className="flex flex-wrap justify-center gap-2 mb-8">
                                {[
                                    "Laravel",
                                    "React",
                                    "WordPress",
                                    "PHP",
                                    "JavaScript",
                                    "Docker",
                                ].map((skill) => (
                                    <span
                                        key={skill}
                                        className="bg-border-gray/50 text-gray text-xs font-semibold px-3 py-1.5 rounded-full"
                                    >
                                        {skill}
                                    </span>
                                ))}
                            </div>

                            <div className="w-12 h-px bg-border-gray mx-auto mb-8"></div>

                            {/* Social Links */}
                            <div className="flex justify-center flex-wrap items-center gap-3">
                                <a
                                    href="https://www.linkedin.com/in/ahmar-iftikhar/"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="inline-flex items-center gap-2 px-4 py-2 bg-white border border-border-gray rounded-full text-sm font-semibold text-primary hover:border-gray transition-colors"
                                >
                                    <Linkedin className="w-4 h-4" />
                                    LinkedIn
                                </a>
                                <a
                                    href="https://github.com/AhmarIftikhar123"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="inline-flex items-center gap-2 px-4 py-2 bg-white border border-border-gray rounded-full text-sm font-semibold text-primary hover:border-gray transition-colors"
                                >
                                    <Github className="w-4 h-4" />
                                    GitHub
                                </a>
                                <a
                                    href="mailto:coadersworldandais@gmail.com?subject=Project Inquiry&body=Hi, I would like to discuss a project."
                                    aria-label="Send email to coadersworldandais@gmail.com"
                                    className="inline-flex items-center gap-2 px-4 py-2 bg-white border border-border-gray rounded-full text-sm font-semibold text-primary hover:border-gray transition-colors"
                                >
                                    <Mail className="w-4 h-4" />
                                    Email
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <DisclaimerBanner />
        </TaxCalculatorLayout>
    );
}
