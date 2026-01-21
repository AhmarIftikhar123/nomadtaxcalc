"use client";

import React from "react";
import { useForm } from "@inertiajs/react";
import Footer from "@/Components/Footer";
import Sidebar from "@/Components/Sidebar";
import TopBar from "@/Components/TopBar";
import Form1Summary from "@/Components/TaxCalculator/Form1Summary";
import Step2Form from "@/Components/TaxCalculator/Step2Form";

export default function Step2({ auth, step1Data, countries }) {
    const isAuthenticated = auth?.user;

    const { data, setData, post, processing, errors } = useForm({
        residency_periods: [],
        ...step1Data,
    });

    const handleSubmit = () => {
        post(route("tax-calculator.step-2"), {
            preserveScroll: true,
        });
    };

    const handleBack = () => {
        window.history.back();
    };

    const handleRecalculate = () => {
        window.location.href = route("tax-calculator.index");
    };

    // If user is authenticated, show dashboard layout
    if (isAuthenticated) {
        return (
            <div className="flex h-screen bg-light">
                {/* Sidebar */}
                <Sidebar user={auth.user} />

                {/* Main Content */}
                <div className="flex-1 ml-64 flex flex-col">
                    {/* Top Bar */}
                    <TopBar
                        title="Residency Details"
                        onRecalculate={handleRecalculate}
                    />

                    {/* Page Content */}
                    <main className="flex-1 overflow-y-auto">
                        <div className="max-w-4xl mx-auto px-6 md:px-8 py-12">
                            {/* Progress Section */}
                            <div className="mb-12 flex justify-between items-end">
                                <div>
                                    <h1 className="text-4xl md:text-5xl font-bold text-primary mb-2">
                                        Residency Details
                                    </h1>
                                    <p className="text-lg text-gray">
                                        Step 2 of 5: Where did you live this
                                        year?
                                    </p>
                                </div>
                                <div className="text-right">
                                    <p className="text-sm font-bold text-primary mb-2">
                                        40% Completed
                                    </p>
                                    <div className="w-32 h-1.5 bg-border-gray rounded-full overflow-hidden">
                                        <div className="w-2/5 h-full bg-primary" />
                                    </div>
                                </div>
                            </div>

                            {/* Form1 Summary */}
                            <Form1Summary formData={step1Data} />

                            {/* Step2 Form Card */}
                            <div className="bg-white rounded-xl border border-border-gray p-8 md:p-12 shadow-sm">
                                <Step2Form
                                    data={data}
                                    setData={setData}
                                    errors={errors}
                                    processing={processing}
                                    onSubmit={handleSubmit}
                                    onBack={handleBack}
                                />
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
            {/* Top Bar */}
            <TopBar
                title="Residency Details"
                onRecalculate={handleRecalculate}
            />
            <main className="min-h-screen bg-light">
                <div className="max-w-4xl mx-auto px-6 md:px-8 py-12">
                    {/* Progress Section */}
                    <div className="mb-12 flex justify-between items-end">
                        <div>
                            <h1 className="text-4xl md:text-5xl font-bold text-primary mb-2">
                                Residency Details
                            </h1>
                            <p className="text-lg text-gray">
                                Step 2 of 5: Where did you live this year?
                            </p>
                        </div>
                        <div className="text-right">
                            <p className="text-sm font-bold text-primary mb-2">
                                40% Completed
                            </p>
                            <div className="w-32 h-1.5 bg-border-gray rounded-full overflow-hidden">
                                <div className="w-2/5 h-full bg-primary" />
                            </div>
                        </div>
                    </div>

                    {/* Form1 Summary */}
                    {/* <Form1Summary formData={step1Data} /> */}
                    <Form1Summary
                        formData={{
                            annual_income: 85000,
                            currency: "USD",
                            country_of_citizenship: "United States",
                        }}
                    />

                    {/* Step2 Form Card */}
                    <div className="bg-white rounded-xl border border-border-gray p-8 md:p-12 shadow-sm">
                        <Step2Form
                            data={data}
                            setData={setData}
                            errors={errors}
                            processing={processing}
                            onSubmit={handleSubmit}
                            onBack={handleBack}
                        />
                    </div>
                </div>
            </main>
            <Footer />
        </>
    );
}
