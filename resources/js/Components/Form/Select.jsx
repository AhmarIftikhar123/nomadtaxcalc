import React, { useState } from "react";
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
} from "@headlessui/react";
import { Check, ChevronDown, X } from "lucide-react";

export default function Select({
    label,
    labelIcon,
    value,
    onChange,
    options = [],
    error,
    placeholder = "Search or select...",
    className = "",
    disabled = false,
    helpText,
    creatable = false,
    onCreateOption,
}) {
    const [query, setQuery] = useState("");

    // Find the selected option
    const selectedOption = options.find((opt) => opt.value === value) || null;

    // Filter options based on search query
    const filteredOptions =
        query === ""
            ? options
            : options.filter((option) =>
                  option.label.toLowerCase().includes(query.toLowerCase()),
              );

    // Handle selection
    const handleChange = (selectedValue) => {
        onChange(selectedValue);
        setQuery(""); // Clear search after selection
    };

    // Handle clear button
    const handleClear = (e) => {
        e.stopPropagation();
        onChange("");
        setQuery("");
    };

    return (
        <div className={`w-full ${className}`}>
            {/* Label */}
            {label && (
                <label className="flex items-center gap-2 text-sm font-semibold text-primary mb-2">
                    {label}
                    {labelIcon}
                </label>
            )}

            <div className="relative">
                <Combobox
                    value={value}
                    onChange={handleChange}
                    disabled={disabled}
                    immediate // Opens dropdown immediately on focus
                >
                    {({ open }) => (
                        <>
                            <div className="relative">
                                {/* Input Field */}
                                <ComboboxInput
                                    className={`relative w-full cursor-pointer bg-white py-3 px-4 pr-2 text-left border rounded-lg text-base font-sans focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 ${
                                        error
                                            ? "border-red-500"
                                            : open
                                              ? "border-primary ring-2 ring-primary"
                                              : "border-border-gray hover:border-gray"
                                    } ${disabled ? "opacity-50 cursor-not-allowed bg-light" : ""}`}
                                    displayValue={(val) =>
                                        selectedOption?.label || ""
                                    }
                                    onChange={(event) =>
                                        setQuery(event.target.value)
                                    }
                                    placeholder={placeholder}
                                    autoComplete="off"
                                />

                                {/* Clear Button */}
                                {selectedOption && !disabled && (
                                    <button
                                        type="button"
                                        onClick={handleClear}
                                        className="absolute inset-y-0 right-10 flex items-center pr-2 hover:text-primary transition-colors"
                                    >
                                        <X
                                            className="h-4 w-4 text-gray-400 hover:text-primary"
                                            aria-hidden="true"
                                        />
                                    </button>
                                )}

                                {/* Dropdown Button */}
                                <ComboboxButton className="absolute inset-y-0 right-0 flex items-center pr-4">
                                    <ChevronDown
                                        className={`h-5 w-5 text-gray-400 transition-transform duration-200 ${
                                            open ? "rotate-180" : ""
                                        }`}
                                        aria-hidden="true"
                                    />
                                </ComboboxButton>
                            </div>

                            {/* Options Dropdown */}
                            <ComboboxOptions
                                anchor="bottom"
                                transition
                                className="absolute z-50 mt-1 max-h-60 w-[var(--input-width)] overflow-auto rounded-lg bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none transition duration-100 ease-in data-[leave]:data-[closed]:opacity-0"
                            >
                                {filteredOptions.length === 0 && !creatable ? (
                                    <div className="relative cursor-default select-none py-3 px-4 text-gray-400">
                                        {query === ""
                                            ? "No options available"
                                            : "No results found"}
                                    </div>
                                ) : (
                                    <>
                                        {filteredOptions.map((option) => (
                                            <ComboboxOption
                                                key={option.value}
                                                value={option.value}
                                                className="relative cursor-pointer select-none py-3 pl-4 pr-10 data-[focus]:bg-light data-[focus]:text-primary text-gray-900"
                                            >
                                                {({ selected }) => (
                                                    <>
                                                        <span
                                                            className={`block truncate ${
                                                                selected
                                                                    ? "font-semibold"
                                                                    : "font-normal"
                                                            }`}
                                                        >
                                                            {option.label}
                                                        </span>
                                                        {selected && (
                                                            <span className="absolute inset-y-0 right-0 flex items-center pr-3 text-primary">
                                                                <Check
                                                                    className="h-5 w-5"
                                                                    aria-hidden="true"
                                                                />
                                                            </span>
                                                        )}
                                                    </>
                                                )}
                                            </ComboboxOption>
                                        ))}

                                        {/* Creatable: Show "Create" option if query exists and no exact match */}
                                        {creatable &&
                                            query !== "" &&
                                            !filteredOptions.some(
                                                (opt) =>
                                                    opt.label.toLowerCase() ===
                                                    query.toLowerCase(),
                                            ) && (
                                                <ComboboxOption
                                                    value={query}
                                                    className="relative cursor-pointer select-none py-3 pl-4 pr-10 data-[focus]:bg-primary data-[focus]:bg-opacity-10 text-primary border-t border-border-gray"
                                                >
                                                    <span className="block truncate font-medium">
                                                        Create: "{query}"
                                                    </span>
                                                </ComboboxOption>
                                            )}
                                    </>
                                )}
                            </ComboboxOptions>
                        </>
                    )}
                </Combobox>
            </div>

            {/* Error Message */}
            {error && (
                <p className="mt-2 text-sm text-red-600" role="alert">
                    {error}
                </p>
            )}

            {/* Help Text */}
            {helpText && !error && (
                <p className="mt-2 ms-2 text-sm text-gray-600">{helpText}</p>
            )}
        </div>
    );
}
