"use client";

import React, { useState, useImperativeHandle, forwardRef } from "react";
import { Plus, X, DollarSign, Percent, AlertCircle } from "lucide-react";
import Select from "@/Components/Form/Select";

const TaxTypeSelector = forwardRef(function TaxTypeSelector({
    countryName,
    countryId,
    value = [],
    onChange,
    availableTaxTypes = [],
    className = "",
}, ref) {
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
    const [validationErrors, setValidationErrors] = useState([]);

    // ── Toggle: clear entries when unchecking ─────────────────────────────────
    const handleToggle = () => {
        const next = !isExpanded;
        setIsExpanded(next);
        if (!next) {
            // User collapsed the panel — wipe entries so they don't pollute calc
            setSelectedTaxTypes([]);
            setValidationErrors([]);
            onChange([]);
        }
    };

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
        setValidationErrors((prev) => prev.filter((eId) => eId !== id));
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
        // Clear validation error for this entry once user starts filling it
        setValidationErrors((prev) => prev.filter((eId) => eId !== id));
        onChange(updated);
    };

    // ── Validation: check all entries before parent form submission ───────────
    // Expose via a ref-compatible pattern — called by checking the DOM dataset.
    // We piggy-back on a data attribute that Step2Form reads before submitting.
    const validate = () => {
        if (!isExpanded) return true;
        const errors = [];
        for (const entry of selectedTaxTypes) {
            const missingName =
                entry.is_custom
                    ? !entry.custom_name || entry.custom_name.trim() === ""
                    : !entry.tax_type_id || entry.tax_type_id === "";
            const missingAmount =
                entry.amount === "" ||
                entry.amount === null ||
                entry.amount === undefined;

            if (missingName || missingAmount) {
                errors.push(entry.id);
            }
        }
        setValidationErrors(errors);
        return errors.length === 0;
    };

    // ── Expose validate() via ref so Step2Form can call it before submit ─────
    useImperativeHandle(ref, () => ({
        validate,
    }), [selectedTaxTypes, isExpanded]);

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
        <div
            className={`${className}`}
            // Expose validate fn on the DOM element so Step2Form can call it
            ref={(el) => {
                if (el) el.__validateCustomTaxes = validate;
            }}
        >
            {/* Toggle Button */}
            <button
                type="button"
                data-tour="step2-tax-types"
                onClick={handleToggle}
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
                            hasError={validationErrors.includes(entry.id)}
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
}); // end forwardRef

export default TaxTypeSelector;

// Individual Tax Type Entry Component
function TaxTypeEntry({
    entry,
    hasError,
    availableOptions,
    availableTaxTypes,
    onUpdate,
    onRemove,
}) {
    // Build options list that includes the currently selected option (if predefined)
    const allOptions = React.useMemo(() => {
        if (!entry.is_custom && entry.tax_type_id) {
            const alreadyExists = availableOptions.some(
                (opt) => opt.value === entry.tax_type_id,
            );

            if (!alreadyExists) {
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

        if (entry.is_custom && entry.custom_name) {
            return [
                { value: entry.custom_name, label: entry.custom_name },
                ...availableOptions,
            ];
        }

        return availableOptions;
    }, [entry, availableOptions, availableTaxTypes]);

    const missingName =
        entry.is_custom
            ? !entry.custom_name || entry.custom_name.trim() === ""
            : !entry.tax_type_id || entry.tax_type_id === "";
    const missingAmount =
        entry.amount === "" ||
        entry.amount === null ||
        entry.amount === undefined;

    return (
        <div
            className={`bg-white rounded-lg border p-4 space-y-3 ${
                hasError ? "border-red-400 bg-red-50/30" : "border-border-gray"
            }`}
        >
            {/* Validation banner */}
            {hasError && (
                <div className="flex items-center gap-2 text-red-600 text-sm font-medium">
                    <AlertCircle className="w-4 h-4 flex-shrink-0" />
                    {missingName && missingAmount
                        ? "Please select a tax type and enter a rate/amount."
                        : missingName
                        ? "Please select or name a tax type."
                        : "Please enter a rate or amount."}
                </div>
            )}

            {/* Header with Remove Button */}
            <div className="flex items-start justify-between gap-2 relative">
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
                            const isPredefined = availableTaxTypes.some(
                                (taxType) => taxType.id.toString() === value,
                            );

                            if (isPredefined) {
                                onUpdate({
                                    tax_type_id: value,
                                    is_custom: false,
                                    custom_name: "",
                                });
                            } else {
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
                                ? `Custom tax: "${entry.custom_name}" — fully custom, applied as-is`
                                : entry.amount !== "" && entry.amount !== null && entry.amount !== undefined
                                ? `⚠️ This adds ${entry.amount}% on top of the standard system calculation for this tax type`
                                : "Leave blank to use the standard brackets/rates, or enter a value to add an extra amount"
                        }
                        hasError={hasError && missingName}
                    />
                </div>

                {/* Remove Button */}
                <button
                    type="button"
                    onClick={onRemove}
                    className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors md:relative md:top-0 md:right-0 absolute top-[-7%] right-[-7%]"
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
                <div className="flex flex-col sm:flex-row gap-2 sm:gap-3">
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
                        className={`w-full px-4 py-3 pr-12 border rounded-lg text-base font-sans focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition ${
                            hasError && missingAmount
                                ? "border-red-400 bg-red-50/30"
                                : "border-border-gray"
                        }`}
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
