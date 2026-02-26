"use client";

import React, { useEffect, useRef } from "react";
import { Link2, X, Copy, Check, ExternalLink, Clock } from "lucide-react";

/**
 * ShareLinkModal — reusable modal for displaying and copying a shareable link.
 *
 * Props:
 *   isOpen   : boolean  — controls visibility
 *   onClose  : fn       — called when user closes the modal
 *   shareUrl : string   — the URL to display/copy
 *   expiresOn: string   — human-readable expiry date  e.g. "March 27, 2026"
 */
export default function ShareLinkModal({
    isOpen,
    onClose,
    shareUrl,
    expiresOn,
}) {
    const [copied, setCopied] = React.useState(false);
    const inputRef = useRef(null);

    // Reset copied state when modal reopens
    useEffect(() => {
        if (isOpen) setCopied(false);
    }, [isOpen, shareUrl]);

    // Close on Escape key
    useEffect(() => {
        if (!isOpen) return;
        const handler = (e) => {
            if (e.key === "Escape") onClose();
        };
        document.addEventListener("keydown", handler);
        return () => document.removeEventListener("keydown", handler);
    }, [isOpen, onClose]);

    const handleCopy = () => {
        if (!shareUrl) return;
        navigator.clipboard.writeText(shareUrl).then(() => {
            setCopied(true);
            setTimeout(() => setCopied(false), 2500);
        });
    };

    const handleSelectAll = () => {
        inputRef.current?.select();
    };

    if (!isOpen) return null;

    return (
        /* Backdrop */
        <div
            className="fixed inset-0 z-50 flex items-center justify-center p-4"
            style={{
                backgroundColor: "rgba(24,25,26,0.55)",
                backdropFilter: "blur(4px)",
            }}
            onClick={(e) => {
                if (e.target === e.currentTarget) onClose();
            }}
        >
            {/* Panel */}
            <div className="bg-white rounded-xl shadow-2xl border border-border-gray w-full max-w-lg animate-in fade-in zoom-in-95 duration-200">
                {/* Header */}
                <div className="flex items-center justify-between px-6 pt-6 pb-4 border-b border-border-gray">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                            <Link2 className="w-5 h-5 text-primary" />
                        </div>
                        <div>
                            <h2 className="text-lg font-bold text-primary leading-tight">
                                Share Your Results
                            </h2>
                            <p className="text-xs text-gray">
                                Anyone with this link can view your results
                            </p>
                        </div>
                    </div>
                    <button
                        onClick={onClose}
                        className="p-2 rounded-lg hover:bg-light text-gray hover:text-primary transition-colors"
                        aria-label="Close"
                    >
                        <X className="w-5 h-5" />
                    </button>
                </div>

                {/* Body */}
                <div className="px-6 py-5 space-y-4">
                    {/* Link input + copy */}
                    <div>
                        <label className="block text-xs font-semibold text-gray uppercase tracking-wider mb-2">
                            Shareable Link
                        </label>
                        <div className="flex gap-2">
                            <input
                                ref={inputRef}
                                type="text"
                                readOnly
                                value={shareUrl || ""}
                                onClick={handleSelectAll}
                                className="flex-1 min-w-0 px-4 py-3 text-sm font-mono bg-light border border-border-gray rounded-lg text-primary focus:outline-none focus:border-primary cursor-text truncate"
                            />
                            <button
                                onClick={handleCopy}
                                className={`flex-shrink-0 flex items-center gap-2 px-4 py-3 font-bold text-sm rounded-lg transition-all border-2 ${
                                    copied
                                        ? "bg-green-50 border-green-500 text-green-700"
                                        : "bg-primary border-primary text-light hover:bg-dark"
                                }`}
                            >
                                {copied ? (
                                    <>
                                        <Check className="w-4 h-4" /> Copied!
                                    </>
                                ) : (
                                    <>
                                        <Copy className="w-4 h-4" /> Copy
                                    </>
                                )}
                            </button>
                        </div>
                    </div>

                    {/* Open in new tab */}
                    {shareUrl && (
                        <a
                            href={shareUrl}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="flex items-center gap-2 text-sm text-gray hover:text-primary transition-colors w-fit"
                        >
                            <ExternalLink className="w-4 h-4" />
                            Open link in new tab
                        </a>
                    )}

                    {/* Expiry warning */}
                    {expiresOn && (
                        <div className="flex items-start gap-2.5 px-4 py-3 bg-amber-50 border border-amber-200 rounded-lg">
                            <Clock className="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" />
                            <p className="text-sm text-amber-700">
                                This link{" "}
                                <strong>expires on {expiresOn}</strong>.
                                Generate a new link from your results page any
                                time to refresh the 30-day window.
                            </p>
                        </div>
                    )}
                </div>

                {/* Footer */}
                <div className="px-6 pb-6">
                    <button
                        onClick={onClose}
                        className="w-full py-3 border-2 border-border-gray text-primary font-bold rounded-lg hover:bg-light transition-colors text-sm"
                    >
                        Done
                    </button>
                </div>
            </div>
        </div>
    );
}
