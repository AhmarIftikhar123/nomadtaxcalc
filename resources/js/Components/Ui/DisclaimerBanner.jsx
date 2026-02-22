import React from "react";
import { TriangleAlert } from "lucide-react";

export default function DisclaimerBanner({ className = "" }) {
    return (
        <div
            className={`bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-center gap-3 mb-2 ${className}`}
        >
            <TriangleAlert className="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
            <p className="text-sm font-medium text-amber-800">
                <strong>Estimates only.</strong> Consult a qualified tax
                professional.
            </p>
        </div>
    );
}
