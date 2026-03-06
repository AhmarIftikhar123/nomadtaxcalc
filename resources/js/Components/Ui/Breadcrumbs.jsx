import React from "react";
import { Link } from "@inertiajs/react";
import { ChevronRight, Home } from "lucide-react";

/**
 * Breadcrumbs — Reusable breadcrumb navigation component.
 *
 * @param {Array} items  - Array of { label: string, href?: string }. Last item is the current page.
 * @param {boolean} showHome - Whether to show the Home icon as the first crumb. Default: true.
 * @param {string} className - Additional classes for the wrapper.
 */
export default function Breadcrumbs({
    items = [],
    showHome = true,
    className = "",
}) {
    const allCrumbs = showHome
        ? [{ label: "Home", href: "/" }, ...items]
        : items;

    return (
        <nav
            aria-label="Breadcrumb"
            className={`flex items-center gap-1.5 text-sm ${className}`}
        >
            {allCrumbs.map((crumb, index) => {
                const isLast = index === allCrumbs.length - 1;
                const isHome = index === 0 && showHome;

                return (
                    <React.Fragment key={index}>
                        {index > 0 && (
                            <ChevronRight
                                className="w-3.5 h-3.5 text-gray/50 flex-shrink-0"
                                aria-hidden="true"
                            />
                        )}
                        {isLast ? (
                            <span
                                className="font-medium text-light truncate max-w-[200px]"
                                aria-current="page"
                            >
                                {isHome && (
                                    <Home className="w-3.5 h-3.5 inline-block mr-1 -mt-0.5 text-gray" />
                                )}
                                {crumb.label}
                            </span>
                        ) : crumb.href ? (
                            <Link
                                href={crumb.href}
                                className="text-gray hover:text-primary transition-colors truncate max-w-[160px] flex items-center gap-1"
                            >
                                {isHome && (
                                    <Home className="w-3.5 h-3.5 flex-shrink-0" />
                                )}
                                {isHome ? (
                                    <span className="sr-only">
                                        {crumb.label}
                                    </span>
                                ) : (
                                    crumb.label
                                )}
                            </Link>
                        ) : (
                            <span className="text-gray truncate max-w-[160px]">
                                {crumb.label}
                            </span>
                        )}
                    </React.Fragment>
                );
            })}
        </nav>
    );
}
