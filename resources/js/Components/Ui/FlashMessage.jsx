import React, { useState, useEffect } from "react";
import { CheckCircle, XCircle } from "lucide-react";

/**
 * Auto-fading flash message.
 *
 * @param {"success"|"error"} type
 * @param {string}            message   — text to display
 * @param {number}            fadeAfter — ms before fade starts (default 5000)
 */
export default function FlashMessage({
    type = "success",
    message,
    fadeAfter = 5000,
}) {
    const [visible, setVisible] = useState(true);
    const [fading, setFading] = useState(false);

    useEffect(() => {
        if (!message) return;
        setVisible(true);
        setFading(false);
        const fadeTimer = setTimeout(() => setFading(true), fadeAfter);
        const hideTimer = setTimeout(() => setVisible(false), fadeAfter + 500);
        return () => {
            clearTimeout(fadeTimer);
            clearTimeout(hideTimer);
        };
    }, [message, fadeAfter]);

    if (!visible || !message) return null;

    const isSuccess = type === "success";

    return (
        <div
            className={`flex items-center gap-3 rounded-xl px-6 py-4 mb-6 transition-opacity duration-500 ${
                fading ? "opacity-0" : "opacity-100"
            } ${
                isSuccess
                    ? "bg-green-50 border border-green-200 text-green-800"
                    : "bg-red-50 border border-red-200 text-red-800"
            }`}
        >
            {isSuccess ? (
                <CheckCircle className="w-5 h-5 text-green-600 flex-shrink-0" />
            ) : (
                <XCircle className="w-5 h-5 text-red-600 flex-shrink-0" />
            )}
            <span className="font-medium">{message}</span>
        </div>
    );
}
