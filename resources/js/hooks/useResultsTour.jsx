import React, { useState, useCallback } from "react";
import { Info, MapPin } from "lucide-react";

/**
 * useResultsTour — react-joyride step definitions for the Scenario Comparison Results phase.
 */

const RESULTS_TOUR_SEEN_KEY = "nomadtaxcalc_results_tour_seen";

export function useResultsTour() {
    const [tourActive, setTourActive] = useState(false);
    const [tourKey, setTourKey] = useState(0);

    const steps = [
        // Step 1: Summary Metrics
        {
            target: "[data-tour='results-summary']",
            title: (
                <div className="flex items-center gap-2">
                    <Info className="w-5 h-5 text-primary" />
                    <span>The Final Verdict</span>
                </div>
            ),
            content:
                "Here are your calculated differences! You can see the total tax liability change, the effective tax rate difference, and a breakdown by country in the chart below.",
            placement: "top",
            disableBeacon: true,
        },
        // Step 2: Residency Risk
        {
            target: "[data-tour='results-risk']",
            title: (
                <div className="flex items-center gap-2">
                    <MapPin className="w-5 h-5 text-primary" />
                    <span>Residency Risk Flags</span>
                </div>
            ),
            content:
                "This section alerts you to potential tax traps. We automatically check if you are crossing any 183-day thresholds or triggering citizenship-based taxation in either scenario.",
            placement: "top",
            disableBeacon: true,
        },
    ];

    const startResultsTour = useCallback(() => {
        const alreadySeen = localStorage.getItem(RESULTS_TOUR_SEEN_KEY);
        // Only auto-trigger once ever
        if (!alreadySeen) {
            setTourKey((k) => k + 1);
            setTourActive(true);
        }
    }, []);

    const handleTourCallback = useCallback((data) => {
        const { status } = data;
        if (status === "finished" || status === "skipped") {
            setTourActive(false);
            localStorage.setItem(RESULTS_TOUR_SEEN_KEY, "1");
        }
    }, []);

    return { steps, tourActive, tourKey, startResultsTour, handleTourCallback };
}
