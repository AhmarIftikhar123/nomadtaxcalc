import React, { useState } from "react";

export default function FaqAccordion({ faqs }) {
    const [openIndex, setOpenIndex] = useState(null);

    const toggle = (index) => {
        setOpenIndex(openIndex === index ? null : index);
    };

    return (
        <div className="space-y-4">
            {faqs.map((faq, index) => {
                const isOpen = openIndex === index;
                return (
                    <div
                        key={index}
                        className={`border rounded-xl transition-all duration-200 overflow-hidden ${
                            isOpen
                                ? "border-primary bg-light"
                                : "border-border-gray bg-white hover:border-gray"
                        }`}
                    >
                        <button
                            type="button"
                            className="flex justify-between items-center w-full px-6 py-5 focus:outline-none"
                            onClick={() => toggle(index)}
                        >
                            <span
                                className={`font-semibold text-left ${isOpen ? "text-primary" : "text-primary"}`}
                            >
                                {faq.question}
                            </span>
                            <span className="ml-4 flex-shrink-0">
                                <svg
                                    className={`w-5 h-5 transition-transform duration-200 ${
                                        isOpen
                                            ? "rotate-180 text-primary"
                                            : "text-gray"
                                    }`}
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </span>
                        </button>

                        <div
                            className={`transition-all duration-300 ease-in-out ${
                                isOpen
                                    ? "max-h-96 opacity-100"
                                    : "max-h-0 opacity-0"
                            }`}
                        >
                            <div className="px-6 pb-5 pt-0 text-gray text-sm leading-relaxed">
                                {faq.answer}
                            </div>
                        </div>
                    </div>
                );
            })}
        </div>
    );
}
