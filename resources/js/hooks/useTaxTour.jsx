import React, { useState, useCallback, useEffect } from "react";
import {
    Banknote,
    Globe2,
    Calendar,
    Map,
    Plus,
    Zap,
    LayoutPanelTop,
    Hash,
    GitMerge,
    Lock,
} from "lucide-react";

/**
 * useTaxTour — react-joyride step definitions for the Tax Calculator.
 *
 * @param {number} calcStep        - Current calculator step (1 / 2 / 3)
 * @param {boolean} hasPeriods     - True once the user has added ≥1 residency period.
 *                                   Controls whether the tax-types step is injected.
 */

const STEP1_STEPS = [
    {
        target: "[data-tour='step1-income']",
        title: (
            <div className="flex items-center gap-2">
                <Banknote className="w-5 h-5 text-primary" />
                <span>What's your annual income?</span>
            </div>
        ),
        content:
            "Tell us your total yearly income and the currency you earn in. This is the foundation — we use it to calculate exactly how much tax applies to you, not a generic estimate.",
        placement: "bottom",
        disableBeacon: true,
    },
    {
        target: "[data-tour='step1-citizenship']",
        title: (
            <div className="flex items-center gap-2">
                <Globe2 className="w-5 h-5 text-primary" />
                <span>Where are you from?</span>
            </div>
        ),
        content:
            "Your citizenship determines which tax treaties protect you, and whether rules like the US FEIE or the UK split-year treatment apply. It's not where you live — it's your passport.",
        placement: "bottom",
        disableBeacon: true,
    },
    {
        target: "[data-tour='step1-taxyear']",
        title: (
            <div className="flex items-center gap-2">
                <Calendar className="w-5 h-5 text-primary" />
                <span>Which tax year are we calculating?</span>
            </div>
        ),
        content:
            "Tax brackets and treaty rules change year to year. Pick the year you want your calculation for — we support 2025 and 2026.",
        placement: "bottom",
        disableBeacon: true,
    },
];

const STEP2_STEPS_BASE = [
    {
        target: "[data-tour='step2-intro']",
        title: (
            <div className="flex items-center gap-2">
                <Map className="w-5 h-5 text-primary" />
                <span>Where did you spend your year?</span>
            </div>
        ),
        content:
            "This is where it gets interesting. Tell us every country you lived in and how many days — we'll figure out where you're a tax resident, where treaties apply, and where you owe nothing.",
        placement: "bottom",
        disableBeacon: true,
    },
    {
        target: "[data-tour='step2-add-period']",
        title: (
            <div className="flex items-center gap-2">
                <Plus className="w-5 h-5 text-primary" />
                <span>Add each country you visited</span>
            </div>
        ),
        content:
            "Search for a country, enter the days you spent there, and hit Add. Keep adding until you've accounted for your full 365-day year. The progress bar above shows how much of the year is covered.",
        placement: "top",
        disableBeacon: true,
    },
];

const STEP2_TAX_TYPES_STEP = {
    target: "[data-tour='step2-tax-types']",
    title: (
        <div className="flex items-center gap-2">
            <Zap className="w-5 h-5 text-primary" />
            <span>Got a special tax regime? Add it here.</span>
        </div>
    ),
    content:
        'Some countries have special rates for digital nomads. For example, Spain\'s Beckham Law caps tax at 24% flat. Click "Add custom/local taxes", name it (e.g. Beckham Law), and set the percentage — our engine applies it automatically.',
    placement: "top",
    disableBeacon: true,
};

const STEP3_STEPS = [
    {
        target: "[data-tour='step3-tabs']",
        title: (
            <div className="flex items-center gap-2">
                <LayoutPanelTop className="w-5 h-5 text-primary" />
                <span>Your results are organized across 5 tabs</span>
            </div>
        ),
        content:
            "Summary shows your key numbers at a glance. Residency shows per-country status. Treaties & FEIE shows how double-tax agreements and US exclusions protect you. Breakdown is the detailed math. Recommendations tells you how to optimize.",
        placement: "bottom",
        disableBeacon: true,
    },
    {
        target: "[data-tour='step3-metrics']",
        title: (
            <div className="flex items-center gap-2">
                <Hash className="w-5 h-5 text-primary" />
                <span>Your tax numbers, right here</span>
            </div>
        ),
        content:
            "Total tax owed, net income after tax, and your effective tax rate — across every country you lived in this year. These numbers account for your residency status, applicable tax treaties, and any custom rates you added.",
        placement: "bottom",
        disableBeacon: true,
    },
    {
        target: "[data-tour='step3-flow']",
        title: (
            <div className="flex items-center gap-2">
                <GitMerge className="w-5 h-5 text-primary" />
                <span>How we calculated your taxes</span>
            </div>
        ),
        content:
            "This map shows the full calculation pipeline: residency determination → tax per country → treaty resolution → FEIE (if you're a US citizen) → final totals. Every step is traceable.",
        placement: "top",
        disableBeacon: true,
    },
    {
        target: "[data-tour='step3-login-cta']",
        title: (
            <div className="flex items-center gap-2">
                <Lock className="w-5 h-5 text-primary" />
                <span>Save, share & email your results</span>
            </div>
        ),
        content:
            "Log in to save this calculation to your account, generate a public shareable link (valid 30 days), or email the full breakdown to yourself. Your data is never stored until you choose to save it.",
        placement: "bottom",
        disableBeacon: true,
    },
];

const TOUR_SEEN_KEY = "nomadtaxcalc_tour_seen";

export function useTaxTour(calcStep, hasPeriods = false) {
    const [tourActive, setTourActive] = useState(false);
    const [tourKey, setTourKey] = useState(0);

    // Build step list — inject tax-types step only when a period card exists in DOM
    const steps =
        calcStep === 1
            ? STEP1_STEPS
            : calcStep === 2
              ? hasPeriods
                  ? [...STEP2_STEPS_BASE, STEP2_TAX_TYPES_STEP]
                  : STEP2_STEPS_BASE
              : STEP3_STEPS;

    /** Auto-start tour on first-ever visit (no localStorage flag). */
    useEffect(() => {
        const alreadySeen = localStorage.getItem(TOUR_SEEN_KEY);
        if (!alreadySeen && [1, 2, 3].includes(calcStep)) {
            // Small delay so the page fully paints before joyride starts
            const timer = setTimeout(() => {
                setTourKey((k) => k + 1);
                setTourActive(true);
            }, 800);
            return () => clearTimeout(timer);
        }
    }, [calcStep]); // Re-run when navigation between steps happens

    const startTour = useCallback(() => {
        setTourKey((k) => k + 1);
        setTourActive(true);
    }, []);

    const handleTourCallback = useCallback(
        (data) => {
            const { status } = data;
            if (status === "finished" || status === "skipped") {
                setTourActive(false);

                // Only mark the WHOLE tour as seen once the user finishes/skips the results (Step 3) tour.
                // This ensures Steps 1 and 2 auto-start until they've reached the end.
                if (calcStep === 3) {
                    localStorage.setItem(TOUR_SEEN_KEY, "1");
                }
            }
        },
        [calcStep],
    );

    return { steps, tourActive, tourKey, startTour, handleTourCallback };
}
