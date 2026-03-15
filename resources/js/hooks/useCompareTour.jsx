import React, { useState, useCallback, useEffect } from "react";
import {
    ArrowLeft,
    Layers,
    Copy,
    Zap,
    HelpCircle,
} from "lucide-react";

/**
 * useCompareTour — react-joyride step definitions for the Scenario Comparison page.
 *
 * @param {boolean} isDefaultData - True if user is viewing default sample data (e.g. 100k USD, no citizenship).
 *                                  Used to prioritize the "Go Back" step to personalize their profile.
 */

const COMPARE_TOUR_SEEN_KEY = "nomadtaxcalc_compare_tour_seen";

export function useCompareTour(isDefaultData = false) {
    const [tourActive, setTourActive] = useState(false);
    const [tourKey, setTourKey] = useState(0);

    const steps = [
        // Step 1: Personalized Profile Alert (Only if default data)
        ...(isDefaultData
            ? [
                  {
                      target: "[data-tour='compare-back']",
                      title: (
                          <div className="flex items-center gap-2">
                              <ArrowLeft className="w-5 h-5 text-primary" />
                              <span>Personalize your profile first</span>
                          </div>
                      ),
                      content:
                          "You're currently seeing a sample calculation with default data ($100k income). For accurate results, we recommend going back to Step 1 to set your actual income, currency, and citizenship.",
                      placement: "bottom",
                      disableBeacon: true,
                  },
              ]
            : []),

        // Step 2: Scenario A (Baseline)
        {
            target: "[data-tour='scenario-a']",
            title: (
                <div className="flex items-center gap-2">
                    <Layers className="w-5 h-5 text-primary" />
                    <span>Scenario A (The Baseline)</span>
                </div>
            ),
            content:
                "This side represents your primary plan — often your current travel itinerary or the most likely path. It's the baseline we'll compare everything else against.",
            placement: "right",
            disableBeacon: true,
        },

        // Step 3: Scenario B (Alternative)
        {
            target: "[data-tour='scenario-b']",
            title: (
                <div className="flex items-center gap-2">
                    <Copy className="w-5 h-5 text-primary" />
                    <span>Scenario B (The Alternative)</span>
                </div>
            ),
            content:
                "Here's where you experiment. What if you stayed in Spain for 180 days instead of 200? What if you added a week in a tax-free country? Adjust the countries and days here to see the difference.",
            placement: "left",
            disableBeacon: true,
        },

        // Step 4: Compare CTA
        {
            target: "[data-tour='compare-run']",
            title: (
                <div className="flex items-center gap-2">
                    <Zap className="w-5 h-5 text-primary" />
                    <span>Run the Comparison</span>
                </div>
            ),
            content:
                "Once your scenarios are set, hit this button. Our engine will calculate full tax liability, treaty credits, and residency status for BOTH scenarios and show you the 'winner' with the lowest tax.",
            placement: "top",
            disableBeacon: true,
        },
    ];

    /** Auto-start tour logic */
    useEffect(() => {
        const alreadySeen = localStorage.getItem(COMPARE_TOUR_SEEN_KEY);
        
        // Auto-start if:
        // 1. User has never seen the tour
        // 2. OR they land here with default data (important to guide them back)
        if (!alreadySeen || isDefaultData) {
            const timer = setTimeout(() => {
                // If it's just the default data trigger and they've seen the general tour,
                // maybe they know what they are doing, BUT the user request was specific
                // about showing the back button if data isn't personalized.
                setTourKey((k) => k + 1);
                setTourActive(true);
            }, 800);
            return () => clearTimeout(timer);
        }
    }, [isDefaultData]);

    const startTour = useCallback(() => {
        setTourKey((k) => k + 1);
        setTourActive(true);
    }, []);

    const handleTourCallback = useCallback((data) => {
        const { status } = data;
        if (status === "finished" || status === "skipped") {
            setTourActive(false);
            localStorage.setItem(COMPARE_TOUR_SEEN_KEY, "1");
        }
    }, []);

    return { steps, tourActive, tourKey, startTour, handleTourCallback };
}
