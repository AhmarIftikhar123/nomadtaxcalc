import React from "react";
import { Trophy } from "lucide-react";

export default function ScenarioComparisonSection() {
    return (
        <section className="w-full bg-white dark:bg-dark py-20 px-6 md:px-20 lg:px-40">
            {/* Outer Wrapper (Light Gray Box) */}
            <div className="max-w-[1024px] mx-auto bg-[#faf9f8] dark:bg-dark-surface rounded-[32px] p-6 lg:p-12 flex flex-col gap-10 shadow-sm border border-black/5 dark:border-white/5">
                
                {/* TOP: text + des here */}
                <div className="flex flex-col gap-5 text-center items-center w-full">
                    <h2 className="text-green-500 dark:text-green-400 text-xs md:text-sm font-black tracking-widest uppercase">
                        SCENARIO COMPARISON
                    </h2>
                    <h2 className="text-primary dark:text-white text-[44px] md:text-3xl lg:text-4xl font-black leading-[1.05] tracking-tighter">
                        Should you have traveled differently?
                    </h2>
                    <p className="text-gray dark:text-gray-400 md:text-lg font-normal leading-relaxed mt-2 max-w-[640px]">
                        Change a few days, see exactly how much tax you'd save.
                        The only tool that makes this instant.
                    </p>
                </div>

                {/* MIDDLE: comparison here */}
                <div className="flex flex-col gap-8">
                    
                    {/* Headers Row (if decoupled from cards) or we can place them right above the boxes */}
                    <div className="flex flex-col md:flex-row gap-6">
                        
                        {/* Scenario A Column */}
                        <div className="flex-1 flex flex-col gap-4">
                            <div className="px-2">
                                <span className="text-primary dark:text-white text-[10px] sm:text-[11px] font-black tracking-[0.15em] uppercase">
                                    SCENARIO A · CURRENT
                                </span>
                                <h3 className="text-primary dark:text-white mt-1.5 leading-none font-black text-2xl lg:text-[28px] tracking-tight">
                                    Your Actual Travel
                                </h3>
                            </div>

                            {/* Card Box */}
                            <div className="bg-white dark:bg-dark border-[1.5px] border-green-500 rounded-[16px] p-6 lg:p-8 flex flex-col shadow-sm h-full">
                                <div className="flex flex-col gap-6 mb-10">
                                    {/* Turkey Row */}
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center gap-3">
                                            <span className="text-[13px] font-black text-gray-800 dark:text-gray-300 uppercase tracking-wide">TR</span>
                                            <span className="text-[16px] font-medium text-primary dark:text-white">Turkey</span>
                                        </div>
                                        <div className="flex items-center justify-end gap-3.5 text-right">
                                            <div className="flex flex-col items-center justify-center bg-red-100/80 text-red-500 dark:bg-red-900/40 dark:text-red-400 rounded px-2 py-1 leading-[1.1]">
                                                <span className="text-[9px] font-black uppercase tracking-widest">Tax</span>
                                                <span className="text-[9px] font-black uppercase tracking-widest">Resident</span>
                                            </div>
                                            <div className="flex flex-col items-end leading-none gap-[1px]">
                                                <span className="text-[17px] font-black leading-none dark:text-white text-primary">200</span>
                                                <span className="text-[11px] font-black dark:text-white text-primary">days</span>
                                            </div>
                                        </div>
                                    </div>
                                    {/* Japan Row */}
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center gap-3">
                                            <span className="text-[13px] font-black text-gray-800 dark:text-gray-300 uppercase tracking-wide">JP</span>
                                            <span className="text-[16px] font-medium text-primary dark:text-white">Japan</span>
                                        </div>
                                        <div className="flex items-center justify-end gap-3.5 text-right">
                                            <div className="flex flex-col items-center justify-center bg-yellow-100/80 text-yellow-600 dark:bg-yellow-900/40 dark:text-yellow-400 rounded px-2 py-1 leading-[1.1]">
                                                <span className="text-[9px] font-black uppercase tracking-widest">Near</span>
                                                <span className="text-[9px] font-black uppercase tracking-widest">Threshold</span>
                                            </div>
                                            <div className="flex flex-col items-end leading-none gap-[1px]">
                                                <span className="text-[17px] font-black leading-none dark:text-white text-primary">165</span>
                                                <span className="text-[11px] font-black dark:text-white text-primary">days</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="mt-auto pt-6 border-t-[1.5px] border-gray-100 dark:border-white/10 flex items-center justify-between">
                                    <span className="text-[18px] font-black text-primary dark:text-white tracking-tight">Tax Liability</span>
                                    <span className="text-[24px] font-black text-red-400/80 dark:text-red-400/60 line-through decoration-[2.5px] decoration-red-400 opacity-90 tracking-tight">$72,089</span>
                                </div>
                            </div>
                        </div>

                        {/* Scenario B Column */}
                        <div className="flex-1 flex flex-col gap-4">
                            <div className="px-2">
                                <span className="text-primary dark:text-white text-[10px] sm:text-[11px] font-black tracking-[0.15em] uppercase">
                                    SCENARIO B · WHAT IF...
                                </span>
                                <h3 className="text-primary dark:text-white mt-1.5 leading-none font-black text-2xl lg:text-[28px] tracking-tight">
                                    Optimized Travel
                                </h3>
                            </div>

                            {/* Card Box */}
                            <div className="bg-white dark:bg-dark border-[1.5px] border-gray-800 dark:border-gray-500 rounded-[16px] p-6 lg:p-8 flex flex-col shadow-sm h-full">
                                <div className="flex flex-col gap-6 mb-10">
                                    {/* Spain Row */}
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center gap-3">
                                            <span className="text-[13px] font-black text-gray-800 dark:text-gray-300 uppercase tracking-wide">ES</span>
                                            <span className="text-[16px] font-medium text-primary dark:text-white">Spain</span>
                                        </div>
                                        <div className="flex items-center justify-end gap-3.5 text-right">
                                            <div className="flex flex-col items-center justify-center bg-green-100/80 text-green-700 dark:bg-green-900/40 dark:text-green-400 rounded px-2 py-1 leading-[1.1]">
                                                <span className="text-[9px] font-black uppercase tracking-widest">Tax</span>
                                                <span className="text-[9px] font-black uppercase tracking-widest">Resident</span>
                                            </div>
                                            <div className="flex flex-col items-end leading-none gap-[1px]">
                                                <span className="text-[17px] font-black leading-none dark:text-white text-primary">365</span>
                                                <span className="text-[11px] font-black dark:text-white text-primary">days</span>
                                            </div>
                                        </div>
                                    </div>
                                    {/* Turkey Row */}
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center gap-3">
                                            <span className="text-[16px] font-medium text-gray-400 dark:text-gray-500">Turkey — Not visited</span>
                                        </div>
                                        <div className="flex flex-col items-end leading-none gap-[1px] mr-1 text-gray-400 dark:text-gray-500">
                                            <span className="text-[17px] font-black leading-none">—</span>
                                        </div>
                                    </div>
                                </div>

                                <div className="mt-auto pt-6 border-t-[1.5px] border-gray-100 dark:border-white/10 flex items-center justify-between">
                                    <span className="text-[18px] font-black text-primary dark:text-white tracking-tight">Tax Liability</span>
                                    <span className="text-[24px] font-black text-green-500 dark:text-green-400 tracking-tight">$56,667</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {/* BOTTOM: results here (Savings Banner) */}
                <div className="w-full bg-primary rounded-[18px] p-6 lg:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 text-white shadow-sm mt-2">
                    <div className="flex flex-col gap-3 relative">
                        <div className="flex flex-row items-center gap-2.5">
                            <Trophy className="w-6 h-6 text-yellow-300 shrink-0" strokeWidth={2.5} />
                            <span className="font-black text-[17px] lg:text-[19px] tracking-tight leading-none pt-0.5">
                                Scenario B saves you significantly more money
                            </span>
                        </div>
                        <p className="text-green-50 text-[14px] font-medium leading-relaxed max-w-[480px]">
                            200 fewer days in Turkey drops you below the threshold — no tax residency triggered
                        </p>
                    </div>
                    <div className="flex flex-col items-start md:items-end leading-none shrink-0">
                        <span className="text-[36px] md:text-[44px] font-black tracking-tighter leading-none">$15,422</span>
                        <span className="text-[13px] font-black text-green-200 uppercase tracking-[0.25em] mt-2 mr-1">SAVED</span>
                    </div>
                </div>

            </div>
        </section>
    );
}
