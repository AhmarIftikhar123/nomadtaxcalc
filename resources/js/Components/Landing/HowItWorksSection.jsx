import React from "react";
import { Calculator, FileCheckCorner, Globe } from "lucide-react";

export default function HowItWorksSection({ howItWorks }) {
    const iconMap = {
        payments: <Calculator size={48} />,
        public: <Globe size={48} />,
        description: <FileCheckCorner  size={48} />,
    };

    return (
        <section
            id="how-it-works"
            className="bg-light dark:bg-dark-surface px-6 py-20 lg:px-40"
        >
            <div className="max-w-[960px] mx-auto">
                <h2 className="text-primary dark:text-white text-[32px] font-bold leading-tight tracking-[-0.015em] mb-12 text-center">
                    How NomadTax Works
                </h2>
                <div className="space-y-16">
                    {howItWorks &&
                        howItWorks.map((step, index) => (
                            <div
                                key={step.id}
                                className={`flex flex-col ${index % 2 === 1 ? "md:flex-row-reverse" : "md:flex-row"} items-center gap-12`}
                            >
                                <div className="flex-1 space-y-4">
                                    <div className="flex items-center gap-4">
                                        <span className="flex items-center justify-center w-10 h-10 rounded-full bg-primary text-white font-bold text-lg">
                                            {step.id}
                                        </span>
                                        <h3 className="text-xl font-bold text-primary dark:text-white">
                                            {step.title}
                                        </h3>
                                    </div>
                                    <p className="text-gray dark:text-gray-400 text-lg leading-relaxed">
                                        {step.description}
                                    </p>
                                </div>
                                <div className="flex-1 w-full h-64 bg-gray-100 dark:bg-dark-elevated rounded-xl flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-700">
                                    {iconMap[step.icon] || (
                                        <svg
                                            className="w-16 h-16 text-primary/20"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                        </svg>
                                    )}
                                </div>
                            </div>
                        ))}
                </div>
            </div>
        </section>
    );
}
