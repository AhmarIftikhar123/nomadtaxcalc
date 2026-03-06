import React from "react";
import Tooltip from "@/Components/Ui/Tooltip";

// ---------------------------------------------------------------------------
// Primitives
// ---------------------------------------------------------------------------

/** Numbered section wrapper */
export function PolicySection({ id, index, title, children }) {
    return (
        <section id={id} className="scroll-mt-28">
            <div className="flex items-start gap-3 mb-4">
                <span className="mt-0.5 flex-shrink-0 w-6 h-6 rounded-md bg-primary/8 text-primary text-[10px] font-bold flex items-center justify-center">
                    {String(index).padStart(2, "0")}
                </span>
                <h2 className="text-xl font-bold text-primary tracking-tight">
                    {title}
                </h2>
            </div>
            <div className="pl-9 space-y-4">{children}</div>
        </section>
    );
}

/** Standard paragraph */
export function Lead({ children }) {
    return <p className="text-sm leading-relaxed text-gray">{children}</p>;
}

/** Bullet list of items: [{ term, description }] or plain strings */
export function PolicyList({ items = [] }) {
    return (
        <ul className="space-y-2 mt-2">
            {items.map((item, i) => (
                <li
                    key={i}
                    className="flex items-start gap-2.5 text-sm text-gray"
                >
                    <span className="mt-2 flex-shrink-0 w-1.5 h-1.5 rounded-full bg-primary/40" />
                    {typeof item === "string" ? (
                        <span className="leading-relaxed">{item}</span>
                    ) : (
                        <span className="leading-relaxed">
                            <strong className="font-semibold text-primary">
                                {item.term}{" "}
                            </strong>
                            {item.description}
                        </span>
                    )}
                </li>
            ))}
        </ul>
    );
}

/** Contact card at the bottom */
export function ContactCard({ fromEmail = "" }) {
    return (
        <div className="mt-2 bg-light border border-border-gray rounded-xl p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div className="flex-1">
                <p className="font-semibold text-primary mb-1">
                    Have questions about this policy?
                </p>
                <p className="text-sm text-gray">
                    Reach out and we'll get back to you promptly.
                </p>
            </div>
            <Tooltip text="Opens your email client" position="top">
                <a
                    href={`mailto:${fromEmail}`}
                    className="flex-shrink-0 inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-light text-sm font-semibold rounded-xl hover:bg-primary/90 transition-all"
                >
                    <svg
                        className="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                        />
                    </svg>
                    {fromEmail}
                </a>
            </Tooltip>
        </div>
    );
}

/** Thin divider */
export function PolicyDivider() {
    return <hr className="border-border-gray" />;
}
