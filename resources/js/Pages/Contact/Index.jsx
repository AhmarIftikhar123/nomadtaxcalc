import React, { useEffect } from "react";
import { Head, useForm, usePage } from "@inertiajs/react";
import TaxCalculatorLayout from "@/Layouts/TaxCalculatorLayout";
import FaqAccordion from "./FaqAccordion";

const FAQS = [
    {
        question: "How accurate is the tax data?",
        answer: "We use the latest IRS brackets and standard deductions for our calculations. However, this tool provides an estimate and should not replace professional tax advice. Your specific situation may involve nuances not covered here.",
    },
    {
        question: "Do you provide personal tax advice?",
        answer: "No, we are not CPAs or financial advisors. We built this tool to help digital nomads baseline their expectations. Please consult a qualified tax professional before filing.",
    },
    {
        question: "How long does it take to get a reply?",
        answer: "Since this is a solo-developed project, please allow up to 3-5 business days for a response to general inquiries and bug reports. We appreciate your patience!",
    },
];

const SUBJECT_OPTIONS = [
    { value: "general", label: "General Inquiry" },
    { value: "bug", label: "Bug Report" },
    { value: "tax_data", label: "Tax Data Update" },
    { value: "feature", label: "Feature Request" },
    { value: "press", label: "Press Inquiry" },
    { value: "partnership", label: "Partnership" },
    { value: "privacy", label: "Privacy Request" },
];

export default function Contact() {
    const { flash } = usePage().props;

    const { data, setData, post, processing, errors, reset, wasSuccessful } =
        useForm({
            name: "",
            email: "",
            subject: "general",
            message: "",
        });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("contact.submit"), {
            onSuccess: () => reset(),
        });
    };

    return (
        <TaxCalculatorLayout title="Contact Us">
            <Head>
                <title>Contact Us — Nomad Tax Calculator</title>
                <meta
                    name="description"
                    content="Get in touch with Nomad Tax Calculator for bug reports, feature requests, or general inquiries."
                />
            </Head>

            {/* ── Hero Section ── */}
            <div className="bg-primary pt-20 pb-16 px-6 md:px-20 lg:px-40 text-center">
                <div className="max-w-3xl mx-auto">
                    <h1 className="text-4xl md:text-5xl font-black text-light tracking-tight mb-4 font-sans">
                        Get in touch
                    </h1>
                    <p className="text-lg text-light/80 max-w-xl mx-auto leading-relaxed">
                        Have a question, spotted an issue, or want to work
                        together? We'd love to hear from you.
                    </p>
                </div>
            </div>

            {/* ── Main Content ── */}
            <div className="px-6 md:px-20 lg:px-40 py-16 md:py-24 bg-white">
                <div className="max-w-6xl mx-auto">
                    <div className="flex flex-col lg:flex-row gap-16 lg:gap-24 items-start">
                        {/* ── Left Column: Form ── */}
                        <div className="flex-1 w-full max-w-2xl">
                            <h2 className="text-2xl font-bold text-primary mb-8 font-sans">
                                Send us a message
                            </h2>

                            {/* Success Message */}
                            {flash?.success && (
                                <div className="mb-6 p-4 rounded-xl bg-green-50 bg-opacity-50 border border-green-200 text-green-800 text-sm font-medium flex items-start gap-3">
                                    <svg
                                        className="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        ></path>
                                    </svg>
                                    <span>{flash.success}</span>
                                </div>
                            )}

                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {/* Name */}
                                    <div className="space-y-2">
                                        <label
                                            htmlFor="name"
                                            className="block text-sm font-semibold text-primary"
                                        >
                                            Full Name
                                        </label>
                                        <input
                                            id="name"
                                            type="text"
                                            value={data.name}
                                            onChange={(e) =>
                                                setData("name", e.target.value)
                                            }
                                            required
                                            className="w-full rounded-lg border-border-gray shadow-sm focus:border-primary focus:ring-primary transition-colors text-primary bg-light"
                                            placeholder="Jane Doe"
                                        />
                                        {errors.name && (
                                            <p className="text-red-500 text-xs font-medium">
                                                {errors.name}
                                            </p>
                                        )}
                                    </div>

                                    {/* Email */}
                                    <div className="space-y-2">
                                        <label
                                            htmlFor="email"
                                            className="block text-sm font-semibold text-primary"
                                        >
                                            Email Address
                                        </label>
                                        <input
                                            id="email"
                                            type="email"
                                            value={data.email}
                                            onChange={(e) =>
                                                setData("email", e.target.value)
                                            }
                                            required
                                            className="w-full rounded-lg border-border-gray shadow-sm focus:border-primary focus:ring-primary transition-colors text-primary bg-light"
                                            placeholder="jane@example.com"
                                        />
                                        {errors.email && (
                                            <p className="text-red-500 text-xs font-medium">
                                                {errors.email}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                {/* Subject */}
                                <div className="space-y-2">
                                    <label
                                        htmlFor="subject"
                                        className="block text-sm font-semibold text-primary"
                                    >
                                        Subject
                                    </label>
                                    <select
                                        id="subject"
                                        value={data.subject}
                                        onChange={(e) =>
                                            setData("subject", e.target.value)
                                        }
                                        className="w-full rounded-lg border-border-gray shadow-sm focus:border-primary focus:ring-primary transition-colors text-primary bg-light"
                                    >
                                        {SUBJECT_OPTIONS.map((opt) => (
                                            <option
                                                key={opt.value}
                                                value={opt.value}
                                            >
                                                {opt.label}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.subject && (
                                        <p className="text-red-500 text-xs font-medium">
                                            {errors.subject}
                                        </p>
                                    )}
                                </div>

                                {/* Message */}
                                <div className="space-y-2">
                                    <label
                                        htmlFor="message"
                                        className="block text-sm font-semibold text-primary"
                                    >
                                        Message
                                    </label>
                                    <textarea
                                        id="message"
                                        rows={6}
                                        value={data.message}
                                        onChange={(e) =>
                                            setData("message", e.target.value)
                                        }
                                        required
                                        className="w-full rounded-lg border-border-gray shadow-sm focus:border-primary focus:ring-primary transition-colors text-primary bg-light resize-y"
                                        placeholder="How can we help you today?"
                                    />
                                    {errors.message && (
                                        <p className="text-red-500 text-xs font-medium">
                                            {errors.message}
                                        </p>
                                    )}
                                </div>

                                {/* Submit */}
                                <div>
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="inline-flex items-center justify-center px-8 py-3.5 border border-transparent rounded-xl shadow-sm text-sm font-bold text-light bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        {processing ? (
                                            <>
                                                <svg
                                                    className="animate-spin -ml-1 mr-3 h-4 w-4 text-white"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <circle
                                                        className="opacity-25"
                                                        cx="12"
                                                        cy="12"
                                                        r="10"
                                                        stroke="currentColor"
                                                        strokeWidth="4"
                                                    ></circle>
                                                    <path
                                                        className="opacity-75"
                                                        fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                    ></path>
                                                </svg>
                                                Sending...
                                            </>
                                        ) : (
                                            "Send Message"
                                        )}
                                    </button>
                                </div>
                            </form>
                        </div>

                        {/* ── Right Column: Info & FAQs ── */}
                        <div className="w-full lg:w-96 flex-shrink-0 space-y-12">
                            {/* Direct Contact */}
                            <div className="bg-light border border-border-gray rounded-2xl p-8 shadow-sm">
                                <h3 className="text-xl font-bold text-primary mb-6 font-sans">
                                    Direct Contact
                                </h3>

                                <div className="space-y-6">
                                    <div className="flex items-start gap-4">
                                        <div className="flex-shrink-0 mt-1">
                                            <svg
                                                className="w-5 h-5 text-gray"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                                ></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p className="text-sm font-semibold text-primary mb-0.5">
                                                General Enquiries
                                            </p>
                                            <a
                                                href="mailto:hello@nomadtaxcalculator.com"
                                                className="text-sm text-primary hover:text-primary/70 transition-colors"
                                            >
                                                hello@nomadtaxcalculator.com
                                            </a>
                                        </div>
                                    </div>

                                    <div className="flex items-start gap-4">
                                        <div className="flex-shrink-0 mt-1">
                                            <svg
                                                className="w-5 h-5 text-gray"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth="2"
                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                                ></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p className="text-sm font-semibold text-primary mb-0.5">
                                                Privacy Requests
                                            </p>
                                            <a
                                                href="mailto:privacy@nomadtaxcalculator.com"
                                                className="text-sm text-primary hover:text-primary/70 transition-colors"
                                            >
                                                privacy@nomadtaxcalculator.com
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* FAQs */}
                            <div>
                                <h3 className="text-xl font-bold text-primary mb-6 font-sans">
                                    Common Questions
                                </h3>
                                <FaqAccordion faqs={FAQS} />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </TaxCalculatorLayout>
    );
}
