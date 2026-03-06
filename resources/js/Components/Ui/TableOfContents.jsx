import React, { useState, useEffect, useRef } from "react";
import { BookOpen } from "lucide-react";

/**
 * TableOfContents — Reusable sticky TOC for long-form content pages.
 *
 * @param {Array}  sections  - Array of { id: string, label: string } matching section IDs in the DOM.
 * @param {string} title     - Heading text. Default: "On This Page".
 * @param {string} className - Additional classes for the wrapper.
 * @param {number} offset    - Scroll offset for Intersection Observer (px from top). Default: 100.
 */
export default function TableOfContents({
    sections = [],
    title = "On This Page",
    className = "",
    offset = 100,
}) {
    const [activeId, setActiveId] = useState(sections[0]?.id ?? "");
    const observerRef = useRef(null);

    useEffect(() => {
        if (!sections.length) return;

        const handleIntersect = (entries) => {
            // Find which section is highest on the page and intersecting
            let topEntry = null;
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    if (
                        !topEntry ||
                        entry.boundingClientRect.top <
                            topEntry.boundingClientRect.top
                    ) {
                        topEntry = entry;
                    }
                }
            });
            if (topEntry) setActiveId(topEntry.target.id);
        };

        observerRef.current = new IntersectionObserver(handleIntersect, {
            rootMargin: `-${offset}px 0px -40% 0px`,
            threshold: 0,
        });

        sections.forEach(({ id }) => {
            const el = document.getElementById(id);
            if (el) observerRef.current.observe(el);
        });

        return () => observerRef.current?.disconnect();
    }, [sections, offset]);

    const handleClick = (e, id) => {
        e.preventDefault();
        const el = document.getElementById(id);
        if (!el) return;
        const top =
            el.getBoundingClientRect().top + window.scrollY - offset + 10;
        window.scrollTo({ top, behavior: "smooth" });
        setActiveId(id);
    };

    if (!sections.length) return null;

    return (
        <aside
            className={`sticky top-24 self-start w-full ${className}`}
            aria-label="Table of contents"
        >
            <div className="bg-light border border-border-gray rounded-xl p-5">
                {/* Header */}
                <div className="flex items-center gap-2 mb-4">
                    <BookOpen className="w-4 h-4 text-gray flex-shrink-0" />
                    <span className="text-xs font-semibold uppercase tracking-wider text-gray">
                        {title}
                    </span>
                </div>

                {/* TOC Items */}
                <nav>
                    <ol className="space-y-1">
                        {sections.map((section, index) => {
                            const isActive = activeId === section.id;
                            return (
                                <li key={section.id}>
                                    <a
                                        href={`#${section.id}`}
                                        onClick={(e) =>
                                            handleClick(e, section.id)
                                        }
                                        className={`flex items-start gap-2.5 px-2.5 py-2 rounded-lg text-sm transition-all duration-200 group ${
                                            isActive
                                                ? "bg-primary text-light font-medium"
                                                : "text-gray hover:text-primary hover:bg-border-gray/60"
                                        }`}
                                        aria-current={
                                            isActive ? "location" : undefined
                                        }
                                    >
                                        {/* Number badge */}
                                        <span
                                            className={`flex-shrink-0 w-5 h-5 rounded-md text-[10px] font-bold flex items-center justify-center mt-0.5 transition-colors ${
                                                isActive
                                                    ? "bg-light/20 text-light"
                                                    : "bg-border-gray text-gray group-hover:bg-primary/10 group-hover:text-primary"
                                            }`}
                                        >
                                            {String(index + 1).padStart(2, "0")}
                                        </span>
                                        <span className="leading-snug">
                                            {section.label}
                                        </span>
                                    </a>
                                </li>
                            );
                        })}
                    </ol>
                </nav>
            </div>
        </aside>
    );
}
