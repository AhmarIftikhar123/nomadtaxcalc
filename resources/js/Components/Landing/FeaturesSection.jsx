import React from "react";
import { Globe, Clock4, Calculator, Building2 } from "lucide-react";

export default function FeaturesSection({ features }) {
    const iconMap = {
        language: <Globe />,
        schedule: <Clock4 />,
        payments: <Calculator />,
        business: <Building2 />,
    };

    return (
        <section
            id="features"
            className="w-full bg-white py-20 px-6 md:px-20 lg:px-40"
        >
            <div className="max-w-[1200px] mx-auto">
                {/* Section Header */}
                <div className="flex flex-col gap-6 mb-16 text-center md:text-left">
                    <h2 className="text-green-400 dark:text-white text-[22px] font-medium leading-tight tracking-[-0.015em]">
                        Features
                    </h2>
                    <h2 className="text-primary dark:text-white text-3xl md:text-4xl font-black tracking-tight">
                        Built for the way nomads actually live
                    </h2>
                    <p className="text-gray dark:text-gray-400 text-lg font-normal max-w-[640px]">
                        Every feature targets a real problem that generic tax tools have never solved.
                    </p>
                </div>

                {/* Features Grid */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {features &&
                        features.map((feature) => (
                            <div
                                key={feature.id}
                                className={`group flex flex-col gap-5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-dark-surface p-8 transition-all relative ${
                                    feature.coming_soon
                                        ? "opacity-75"
                                        : "hover:border-primary dark:hover:border-white hover:shadow-lg"
                                }`}
                            >
                                {feature.coming_soon && (
                                    <div className="absolute top-6 right-6 flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-800 pointer-events-none select-none">
                                        <span className="text-sm">🏢</span>
                                        <span className="text-[10px] uppercase tracking-wider font-bold">
                                            Coming Soon
                                        </span>
                                    </div>
                                )}
                                <div className={`flex items-center justify-center w-12 h-12 rounded-lg text-white ${feature.coming_soon ? "bg-gray-400 dark:bg-gray-600 bg-primary" : "bg-primary"}`}>
                                    {iconMap[feature.icon] || (
                                        <svg
                                            className="w-6 h-6"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                        </svg>
                                    )}
                                </div>
                                <div className="flex flex-col gap-2">
                                    <h3 className="text-primary dark:text-white text-xl font-bold leading-tight">
                                        {feature.title}
                                    </h3>
                                    <p className="text-gray dark:text-gray-400 text-sm leading-relaxed">
                                        {feature.description}
                                    </p>
                                </div>
                            </div>
                        ))}
                </div>
            </div>
        </section>
    );
}
