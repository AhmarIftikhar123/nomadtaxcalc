"use client";

import React, { useState } from "react";
import { useForm } from "@inertiajs/react";
import Footer from "@/Components/Footer";
import Step1Form from "@/Components/TaxCalculator/Step1Form";
import Sidebar from "@/Components/Sidebar";
import TopBar from "@/Components/TopBar";

export default function TaxCalculatorIndex({ auth, countries, currencies }) {
    const { data, setData, post, processing, errors } = useForm({
        annual_income: "",
        currency: "USD",
        country_of_citizenship: "",
    });

    const isAuthenticated = auth?.user;

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("tax-calculator.step-1"), {
            preserveScroll: true,
        });
    };

    // If user is authenticated, show dashboard layout with sidebar
    if (isAuthenticated) {
        return (
            <div className="flex h-screen bg-light">
                {/* Sidebar */}
                <Sidebar user={auth.user} />

                {/* Main Content */}
                <div className="flex-1 ml-64 flex flex-col">
                    {/* Page Content */}
                    <main className="flex-1 overflow-y-auto">
                        <div className="max-w-[1200px] mx-auto px-6 md:px-8 py-12">
                            {/* Progress Section */}
                            <div className="mb-12">
                                <p className="text-sm font-semibold text-primary uppercase tracking-wider mb-3">
                                    Income & Citizenship
                                </p>
                                <div className="flex items-center gap-4">
                                    <div className="flex-1 max-w-xs">
                                        <div
                                            className="h-1.5 bg-primary rounded-full"
                                            style={{ width: "33.33%" }}
                                        ></div>
                                    </div>
                                    <span className="text-sm font-semibold text-primary whitespace-nowrap">
                                        Step 1 of 3
                                    </span>
                                </div>
                            </div>

                            {/* Form Card */}
                            <div className="bg-white rounded-xl border border-border-gray p-8 md:p-12 shadow-sm">
                                <h1 className="text-4xl md:text-5xl font-bold text-primary mb-4">
                                    Let's start with the basics
                                </h1>
                                <p className="text-lg text-gray mb-10">
                                    To accurately estimate your tax liability,
                                    we need to know your annual earnings and
                                    your primary country of citizenship.
                                </p>

                                <form onSubmit={handleSubmit}>
                                    <Step1Form
                                        data={data}
                                        setData={setData}
                                        errors={errors}
                                        countries={countries}
                                        currencies={currencies}
                                        processing={processing}
                                    />
                                </form>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        );
    }

    // If user is not authenticated, show public layout
    return (
        <>
            {/* <Header /> */}
            {/* Top Bar */}
            <TopBar
                title="Tax Calculation Results"
            />
            <main className="min-h-screen bg-light">
                <div className="max-w-[1200px] mx-auto px-6 md:px-20 lg:px-40 py-16">
                    {/* Progress Section */}
                    <div className="mb-12">
                        <p className="text-sm font-semibold text-primary uppercase tracking-wider mb-3">
                            Income & Citizenship
                        </p>
                        <div className="flex items-center gap-4">
                            <div className="flex-1">
                                <div
                                    className="h-1.5 bg-primary rounded-full"
                                    style={{ width: "33.33%" }}
                                ></div>
                            </div>
                            <span className="text-sm font-semibold text-primary whitespace-nowrap">
                                Step 1 of 3
                            </span>
                        </div>
                    </div>

                    {/* Form Card */}
                    <div className="bg-white rounded-xl border border-border-gray p-8 md:p-12 shadow-sm">
                        <h1 className="text-4xl md:text-5xl font-bold text-primary mb-4">
                            Let's start with the basics
                        </h1>
                        <p className="text-lg text-gray mb-10">
                            To accurately estimate your tax liability, we need
                            to know your annual earnings and your primary
                            country of citizenship.
                        </p>

                        <form onSubmit={handleSubmit}>
                            <Step1Form
                                data={data}
                                setData={setData}
                                errors={errors}
                                countries={countries}
                                currencies={currencies}
                                processing={processing}
                            />
                        </form>
                    </div>
                </div>
            </main>
            <Footer />
        </>
    );
}
