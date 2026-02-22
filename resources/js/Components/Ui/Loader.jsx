import React from "react";

export default function Loader({ className = "" }) {
    return (
        <div className={`flex flex-row gap-2 ${className}`}>
            <div className="w-3 h-3 rounded-full bg-primary animate-bounce"></div>
            <div className="w-3 h-3 rounded-full bg-primary animate-bounce [animation-delay:-.3s]"></div>
            <div className="w-3 h-3 rounded-full bg-primary animate-bounce [animation-delay:-.5s]"></div>
        </div>
    );
}
