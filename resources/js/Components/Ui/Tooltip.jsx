import React from "react";

export default function Tooltip({
    children,
    text,
    position = "top", // top, bottom, left, right
    delay = 0, // delay in ms before showing tooltip
    className = "",
}) {
    const [isShowing, setIsShowing] = React.useState(false);
    const timeoutRef = React.useRef(null);

    const handleMouseEnter = () => {
        if (delay > 0) {
            timeoutRef.current = setTimeout(() => {
                setIsShowing(true);
            }, delay);
        } else {
            setIsShowing(true);
        }
    };

    const handleMouseLeave = () => {
        if (timeoutRef.current) {
            clearTimeout(timeoutRef.current);
        }
        setIsShowing(false);
    };

    // Position classes for the tooltip
    const positionClasses = {
        top: "bottom-full left-1/2 -translate-x-1/2 mb-3",
        bottom: "top-full left-1/2 -translate-x-1/2 mt-3",
        left: "right-full top-1/2 -translate-y-1/2 mr-3",
        right: "left-full top-1/2 -translate-y-1/2 ml-3",
    };

    // Arrow styles - using border method for crisp arrow
    const arrowStyles = {
        top: {
            bottom: "-6px",
            left: "50%",
            transform: "translateX(-50%)",
            borderLeft: "6px solid transparent",
            borderRight: "6px solid transparent",
            borderTop: "6px solid #22262a",
        },
        bottom: {
            top: "-6px",
            left: "50%",
            transform: "translateX(-50%)",
            borderLeft: "6px solid transparent",
            borderRight: "6px solid transparent",
            borderBottom: "6px solid #22262a",
        },
        left: {
            right: "-6px",
            top: "50%",
            transform: "translateY(-50%)",
            borderTop: "6px solid transparent",
            borderBottom: "6px solid transparent",
            borderLeft: "6px solid #22262a",
        },
        right: {
            left: "-6px",
            top: "50%",
            transform: "translateY(-50%)",
            borderTop: "6px solid transparent",
            borderBottom: "6px solid transparent",
            borderRight: "6px solid #22262a",
        },
    };

    return (
        <div
            className={`relative inline-flex ${className}`}
            onMouseEnter={handleMouseEnter}
            onMouseLeave={handleMouseLeave}
        >
            {children}

            {/* Tooltip */}
            {isShowing && text && (
                <div
                    className={`absolute z-[9999] ${positionClasses[position]} pointer-events-none animate-in fade-in duration-150`}
                    role="tooltip"
                >
                    {/* Tooltip Content */}
                    <div className="relative bg-primary text-light px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap shadow-lg">
                        {text}
                        
                        {/* Arrow */}
                        <div
                            className="absolute w-0 h-0"
                            style={arrowStyles[position]}
                        />
                    </div>
                </div>
            )}
        </div>
    );
}