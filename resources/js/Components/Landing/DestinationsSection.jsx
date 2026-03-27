import React from "react";
import PTFlag from "@/assets/images/flags/pt.svg";
import ESFlag from "@/assets/images/flags/es.svg";
import AEFlag from "@/assets/images/flags/ae.svg";
import MXFlag from "@/assets/images/flags/mx.svg";
import THFlag from "@/assets/images/flags/th.svg";
import GEFlag from "@/assets/images/flags/ge.svg";
import EEFlag from "@/assets/images/flags/ee.svg";
import MTFlag from "@/assets/images/flags/mt.svg";
import { Link } from "@inertiajs/react";

export default function DestinationsSection({ destinations }) {
    const flagMap = {
        pt: PTFlag,
        es: ESFlag,
        ae: AEFlag,
        mx: MXFlag,
        th: THFlag,
        ge: GEFlag,
        ee: EEFlag,
        mt: MTFlag,
    };

    return (
        <section
            id="destinations"
            className="px-6 py-20 bg-white dark:bg-dark-elevated lg:px-40"
        >
            <div className="max-w-[960px] mx-auto">
                <div className="flex md:justify-between flex-col md:flex-row justify-center items-center md:items-end mb-10 gap-4">
                    <div className="text-center md:text-left">
                        <h2 className="text-primary dark:text-white text-[32px] font-bold leading-tight tracking-[-0.015em] ">
                            Popular Destinations
                        </h2>
                        <p className="text-gray dark:text-gray-400 mt-2">
                            Top picks for digital nomads based on tax
                            optimization and quality of life.
                        </p>
                    </div>
                    <Link href={route("tax-calculator.index")} className="px-4 py-2 text-sm font-semibold border border-primary dark:border-white rounded-lg hover:bg-primary hover:text-white dark:hover:bg-white dark:hover:text-primary transition-colors">
                        150+ Destinations
                    </Link>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    {destinations &&
                        destinations.map((destination) => (
                            <div
                                key={destination.id}
                                className="country-card bg-white dark:bg-dark-surface p-5 rounded-xl border border-gray-200 dark:border-gray-700 hover:transform hover:translate-y-[-4px] transition-transform duration-200"
                            >
                                <div className="w-12 h-12 mb-4 bg-gray-100 dark:bg-dark-elevated rounded-lg flex items-center justify-center overflow-hidden">
                                    <img
                                        src={flagMap[destination.flag]}
                                        alt={`${destination.name} Flag`}
                                        className="w-full h-full object-cover"
                                    />
                                </div>
                                <h3 className="font-bold text-lg mb-1 text-primary dark:text-white">
                                    {destination.name}
                                </h3>
                                <p className="text-green-600 dark:text-green-400 font-semibold text-sm">
                                    {destination.taxRate}
                                </p>
                                <div className="mt-3 flex flex-wrap gap-2">
                                    <span className="px-2 py-1 bg-primary/10 text-primary dark:bg-white/10 dark:text-white text-xs rounded-full font-medium">
                                        {destination.visa}
                                    </span>
                                </div>
                            </div>
                        ))}
                </div>
            </div>
        </section>
    );
}
