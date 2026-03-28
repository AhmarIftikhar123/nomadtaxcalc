import React from "react";
import { Head } from "@inertiajs/react";
import LandingLayout from "@/Layouts/LandingLayout";
import HeroSection from "@/Components/Landing/HeroSection";
import FeaturesSection from "@/Components/Landing/FeaturesSection";
import ScenarioComparisonSection from "@/Components/Landing/ScenarioComparisonSection";
import HowItWorksSection from "@/Components/Landing/HowItWorksSection";
import DestinationsSection from "@/Components/Landing/DestinationsSection";
import TestimonialsSection from "@/Components/Landing/TestimonialsSection";
import useStackedCards from "@/hooks/useStackedCards";

export default function LandingPage({
    features,
    destinations,
    testimonials,
    howItWorks,
}) {
    const { container, landingPageWrapper, landingPageContent } =
        useStackedCards();
    return (
        <>
            <Head title="NomadTax - Calculate Your Digital Nomad Taxes" />

            <LandingLayout
                wrapper={landingPageWrapper}
                content={landingPageContent}
            >
                {/* Hero Section */}
                <HeroSection />
                {/* How It Works Section */}
                <HowItWorksSection howItWorks={howItWorks} />

                <main ref={container}>
                    {/* Features Section */}
                    <FeaturesSection features={features} />

                    {/* Scenario Comparison Section */}
                    <ScenarioComparisonSection />

                    {/* Destinations Section */}
                    <DestinationsSection destinations={destinations} />

                    {/* Testimonials Section */}
                    <TestimonialsSection testimonials={testimonials} />
                </main>
            </LandingLayout>
        </>
    );
}
