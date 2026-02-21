"use client";

import React, { useState } from "react";
import { Plus, X, DollarSign, Percent } from "lucide-react";
import Select from "@/Components/Form/Select";

export default function TaxTypeSelector({
    countryName,
    countryId,
    value = [],
    onChange,
    availableTaxTypes = [],
    className = "",
}) {
    // Auto-expand if there are custom taxes or any entry with a filled amount
    const hasCustomEntries = value.some(
        (entry) =>
            entry.is_custom ||
            (entry.amount !== "" &&
                entry.amount !== null &&
                entry.amount !== undefined),
    );
    const [isExpanded, setIsExpanded] = useState(hasCustomEntries);
    const [selectedTaxTypes, setSelectedTaxTypes] = useState(value);

    // Add new tax type entry
    const handleAddTaxType = () => {
        const newEntry = {
            id: Date.now(),
            tax_type_id: "",
            custom_name: "",
            amount_type: "percentage", // 'percentage' or 'flat'
            amount: "",
            is_custom: false,
        };

        const updated = [...selectedTaxTypes, newEntry];
        setSelectedTaxTypes(updated);
        onChange(updated);
    };

    // Remove tax type entry
    const handleRemove = (id) => {
        const updated = selectedTaxTypes.filter((item) => item.id !== id);
        setSelectedTaxTypes(updated);
        onChange(updated);
    };

    // Update tax type entry — accepts (id, field, value) OR (id, fieldsObject)
    const handleUpdate = (id, fieldOrFields, value) => {
        const updated = selectedTaxTypes.map((item) => {
            if (item.id !== id) return item;
            if (typeof fieldOrFields === "object" && fieldOrFields !== null) {
                return { ...item, ...fieldOrFields };
            }
            return { ...item, [fieldOrFields]: value };
        });
        setSelectedTaxTypes(updated);
        onChange(updated);
    };

    // Get tax type options (exclude already selected ones)
    const getAvailableTaxTypeOptions = (currentId) => {
        const selectedIds = selectedTaxTypes
            .filter((item) => item.id !== currentId && !item.is_custom)
            .map((item) => item.tax_type_id);

        return availableTaxTypes
            .filter((taxType) => !selectedIds.includes(taxType.id.toString()))
            .map((taxType) => ({
                value: taxType.id.toString(),
                label: taxType.name,
            }));
    };

    return (
        <div className={`${className}`}>
            {/* Toggle Button */}
            <button
                type="button"
                onClick={() => setIsExpanded(!isExpanded)}
                className="w-full flex items-center justify-between px-4 py-3 bg-light border border-border-gray rounded-lg hover:bg-gray-50 transition-colors"
            >
                <div className="flex items-center gap-2">
                    <input
                        type="checkbox"
                        checked={isExpanded}
                        onChange={() => {}}
                        className="w-4 h-4 text-primary border-border-gray rounded focus:ring-primary"
                    />
                    <span className="text-sm font-medium text-primary">
                        Add custom/local taxes for {countryName}
                    </span>
                </div>
                <span className="text-xs text-gray bg-white px-2 py-1 rounded-md border border-border-gray">
                    Optional
                </span>
            </button>

            {/* Expanded Content */}
            {isExpanded && (
                <div className="mt-4 space-y-4 p-4 bg-gray-50 rounded-lg border border-border-gray">
                    <p className="text-sm text-gray mb-4">
                        Add additional taxes like municipal tax, social
                        security, or create custom tax entries.
                    </p>

                    {/* Tax Type Entries */}
                    {selectedTaxTypes.map((entry) => (
                        <TaxTypeEntry
                            key={entry.id}
                            entry={entry}
                            availableOptions={getAvailableTaxTypeOptions(
                                entry.id,
                            )}
                            availableTaxTypes={availableTaxTypes}
                            onUpdate={(fieldOrFields, value) =>
                                handleUpdate(entry.id, fieldOrFields, value)
                            }
                            onRemove={() => handleRemove(entry.id)}
                        />
                    ))}

                    {/* Add Button */}
                    <button
                        type="button"
                        onClick={handleAddTaxType}
                        className="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white border-2 border-dashed border-primary text-primary font-medium rounded-lg hover:bg-light transition-colors"
                    >
                        <Plus className="w-4 h-4" />
                        Add Tax Type
                    </button>
                </div>
            )}
        </div>
    );
}

// Individual Tax Type Entry Component
function TaxTypeEntry({
    entry,
    availableOptions,
    availableTaxTypes,
    onUpdate,
    onRemove,
}) {
    // Build options list that includes the currently selected option (if predefined)
    // This fixes the display issue where selected options disappear from the dropdown
    const allOptions = React.useMemo(() => {
        // If predefined tax type is selected, add it back to options for display
        if (!entry.is_custom && entry.tax_type_id) {
            // Check if it's already in availableOptions
            const alreadyExists = availableOptions.some(
                (opt) => opt.value === entry.tax_type_id,
            );

            if (!alreadyExists) {
                // Find the actual tax type from availableTaxTypes
                const selectedTaxType = availableTaxTypes.find(
                    (t) => t.id.toString() === entry.tax_type_id,
                );

                const label = selectedTaxType
                    ? selectedTaxType.name
                    : `Tax Type #${entry.tax_type_id}`;

                return [
                    { value: entry.tax_type_id, label },
                    ...availableOptions,
                ];
            }
        }

        // If custom, add it as an option for display
        if (entry.is_custom && entry.custom_name) {
            return [
                { value: entry.custom_name, label: entry.custom_name },
                ...availableOptions,
            ];
        }

        return availableOptions;
    }, [entry, availableOptions, availableTaxTypes]);

    return (
        <div className="bg-white rounded-lg border border-border-gray p-4 space-y-3">
            {/* Header with Remove Button */}
            <div className="flex items-start justify-between gap-2">
                <div className="flex-1 space-y-3">
                    {/* Tax Type Selection (with creatable mode) */}
                    <Select
                        label="Tax Type"
                        value={
                            entry.is_custom
                                ? entry.custom_name
                                : entry.tax_type_id
                        }
                        onChange={(value) => {
                            // Check against ALL availableTaxTypes, not just filtered availableOptions
                            const isPredefined = availableTaxTypes.some(
                                (taxType) => taxType.id.toString() === value,
                            );

                            if (isPredefined) {
                                // Predefined tax type — single batched update
                                onUpdate({
                                    tax_type_id: value,
                                    is_custom: false,
                                    custom_name: "",
                                });
                            } else {
                                // Custom tax name — single batched update
                                onUpdate({
                                    custom_name: value,
                                    is_custom: true,
                                    tax_type_id: "",
                                });
                            }
                        }}
                        options={allOptions}
                        placeholder="Search or create a tax type..."
                        creatable={true}
                        helpText={
                            entry.is_custom
                                ? `Custom tax: "${entry.custom_name}"`
                                : "Search for a tax type or type to create a custom one"
                        }
                    />
                </div>

                {/* Remove Button */}
                <button
                    type="button"
                    onClick={onRemove}
                    className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    title="Remove"
                >
                    <X className="w-5 h-5" />
                </button>
            </div>

            {/* Amount Type Selection */}
            <div>
                <label className="block text-sm font-semibold text-primary mb-2">
                    Tax Type
                </label>
                <div className="flex gap-3">
                    <button
                        type="button"
                        onClick={() => onUpdate("amount_type", "percentage")}
                        className={`flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-lg border-2 transition-all ${
                            entry.amount_type === "percentage"
                                ? "border-primary bg-primary text-white"
                                : "border-border-gray bg-white text-gray hover:border-primary"
                        }`}
                    >
                        <Percent className="w-4 h-4" />
                        <span className="text-sm font-medium">Percentage</span>
                    </button>
                    <button
                        type="button"
                        onClick={() => onUpdate("amount_type", "flat")}
                        className={`flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-lg border-2 transition-all ${
                            entry.amount_type === "flat"
                                ? "border-primary bg-primary text-white"
                                : "border-border-gray bg-white text-gray hover:border-primary"
                        }`}
                    >
                        <DollarSign className="w-4 h-4" />
                        <span className="text-sm font-medium">Flat Amount</span>
                    </button>
                </div>
            </div>

            {/* Amount Input */}
            <div>
                <label className="block text-sm font-semibold text-primary mb-2">
                    {entry.amount_type === "percentage"
                        ? "Rate (%)"
                        : "Annual Amount"}
                </label>
                <div className="relative">
                    <input
                        type="number"
                        value={entry.amount}
                        onChange={(e) => onUpdate("amount", e.target.value)}
                        placeholder={
                            entry.amount_type === "percentage"
                                ? "e.g., 2.5"
                                : "e.g., 1500"
                        }
                        step="any"
                        min="0"
                        className="w-full px-4 py-3 pr-12 border border-border-gray rounded-lg text-base font-sans focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                    />
                    <span className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                        {entry.amount_type === "percentage" ? "%" : "$"}
                    </span>
                </div>
                {entry.amount_type === "flat" && (
                    <p className="mt-1 text-xs text-gray">
                        This will be a fixed annual amount regardless of income
                    </p>
                )}
            </div>
        </div>
    );
}
