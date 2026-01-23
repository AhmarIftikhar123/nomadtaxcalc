import React, { useEffect, useRef } from "react";
import Illustration from "@/assets/images/illustrations/hero-illustration.mp4";

export default function HeroSection() {
    const videRef = useRef(null);

    useEffect(() => {
        if (videRef.current) videRef.current.playbackRate = 0.6;
    }, []);

    return (
        <section className="w-full max-w-[1200px] mx-auto py-2 px-2">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                {/* Left Content */}
                <div className="flex flex-col gap-6">
                    {/* Badges */}
                    <div className="flex gap-3 flex-wrap">
                        <div className="flex h-7 items-center justify-center gap-x-2 rounded-full bg-primary/5 dark:bg-white/10 px-3">
                            <svg
                                className="w-4 h-4"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                            </svg>
                            <p className="text-[10px] font-bold uppercase tracking-wider">
                                Free Forever
                            </p>
                        </div>
                        <div className="flex h-7 items-center justify-center gap-x-2 rounded-full bg-primary/5 dark:bg-white/10 px-3">
                            <svg
                                className="w-4 h-4"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" />
                            </svg>
                            <p className="text-[10px] font-bold uppercase tracking-wider">
                                Updated for 2026
                            </p>
                        </div>
                    </div>

                    {/* Heading & Subheading */}
                    <div className="flex flex-col gap-4">
                        <h1 className="text-primary dark:text-white text-4xl md:text-5xl lg:text-6xl font-black leading-[1.1] tracking-tight">
                            Calculate Your Digital Nomad Taxes in Seconds
                        </h1>
                        <p className="text-gray dark:text-gray-400 text-lg md:text-xl font-normal leading-relaxed max-w-[540px]">
                            Navigate the{" "}
                            <span className="text-primary dark:text-white font-semibold underline decoration-2 underline-offset-4 decoration-primary/20">
                                183-day rule
                            </span>{" "}
                            and international tax implications with ease.
                            Professional tools for the modern remote workforce.
                        </p>
                    </div>

                    {/* CTA Buttons */}
                    <div className="flex flex-col sm:flex-row gap-3 pt-4">
                        <button className="flex min-w-[180px] cursor-pointer items-center justify-center rounded-lg h-12 px-6 bg-primary text-white text-base font-bold shadow-md hover:shadow-lg hover:translate-y-[-1px] active:translate-y-[0px] transition-all">
                            Calculate Your Taxes
                        </button>
                        <button className="flex min-w-[180px] cursor-pointer items-center justify-center rounded-lg h-12 px-6 border border-primary/20 dark:border-white/20 text-primary dark:text-white text-base font-bold hover:bg-primary/5 transition-all">
                            View Tax Guides
                        </button>
                    </div>
                </div>

                {/* Right Content - Illustration */}
                <div className="relative group">
                    <div className="absolute -inset-1 bg-gradient-to-r from-structure-light to-structure rounded-2xl blur opacity-25" />
                    <div className="relative w-full aspect-square  overflow-hidden flex items-center justify-center">
                        <video
                            src={Illustration} // video URL
                            ref={videRef}
                            autoPlay
                            loop
                            muted
                            playsInline
                            className="w-full min-h-[450px] object-cover"
                        />
                    </div>
                </div>
            </div>
        </section>
    );
}
