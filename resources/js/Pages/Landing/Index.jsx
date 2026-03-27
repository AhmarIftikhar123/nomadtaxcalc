import React from "react";
import { Head } from "@inertiajs/react";
import LandingLayout from "@/Layouts/LandingLayout";
import HeroSection from "@/Components/Landing/HeroSection";
import FeaturesSection from "@/Components/Landing/FeaturesSection";
import ScenarioComparisonSection from "@/Components/Landing/ScenarioComparisonSection";
import HowItWorksSection from "@/Components/Landing/HowItWorksSection";
import DestinationsSection from "@/Components/Landing/DestinationsSection";
import TestimonialsSection from "@/Components/Landing/TestimonialsSection";

export default function LandingPage({
    features,
    destinations,
    testimonials,
    howItWorks,
}) {
    return (
        <>
            <Head title="NomadTax - Calculate Your Digital Nomad Taxes" />

            <LandingLayout>
                {/* Hero Section */}
                <HeroSection />

                {/* How It Works Section */}
                <HowItWorksSection howItWorks={howItWorks} />

                {/* Features Section */}
                <FeaturesSection features={features} />

                {/* Scenario Comparison Section */}
                <ScenarioComparisonSection />

                {/* Destinations Section */}
                <DestinationsSection destinations={destinations} />

                {/* Testimonials Section */}
                <TestimonialsSection testimonials={testimonials} />
            </LandingLayout>
        </>
    );
}
