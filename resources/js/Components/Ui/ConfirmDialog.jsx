import React, { useRef } from "react";
import { AlertTriangle, X } from "lucide-react";

/**
 * Reusable confirmation dialog.
 *
 * Props:
 *  - isOpen       {boolean}   Whether the dialog is visible
 *  - onClose      {function}  Called when the user cancels (X or Cancel button or backdrop click)
 *  - onConfirm    {function}  Called when the user clicks the confirm button
 *  - title        {string}    Dialog heading  (default: "Are you sure?")
 *  - message      {string}    Body text       (default: "This action cannot be undone.")
 *  - confirmLabel {string}    Confirm button label (default: "Confirm")
 *  - cancelLabel  {string}    Cancel button label  (default: "Cancel")
 *  - variant      {string}    "danger" | "warning" | "info"  (default: "danger")
 *
 * Usage:
 *   <ConfirmDialog
 *     isOpen={open}
 *     onClose={() => setOpen(false)}
 *     onConfirm={handleDelete}
 *     title="Delete Calculation"
 *     message="This record will be permanently removed."
 *     confirmLabel="Yes, delete"
 *     variant="danger"
 *   />
 */
export default function ConfirmDialog({
    isOpen,
    onClose,
    onConfirm,
    title = "Are you sure?",
    message = "This action cannot be undone.",
    confirmLabel = "Confirm",
    cancelLabel = "Cancel",
    variant = "danger",
}) {
    const overlayRef = useRef(null);

    if (!isOpen) return null;

    // Close when clicking the dark backdrop (not the dialog itself)
    const handleOverlayClick = (e) => {
        if (e.target === overlayRef.current) onClose();
    };

    const variantStyles = {
        danger: {
            icon: "bg-red-100 text-red-600",
            button: "bg-red-600 hover:bg-red-700 text-white focus-visible:ring-red-500",
        },
        warning: {
            icon: "bg-yellow-100 text-yellow-600",
            button: "bg-yellow-500 hover:bg-yellow-600 text-white focus-visible:ring-yellow-400",
        },
        info: {
            icon: "bg-primary/10 text-primary",
            button: "bg-primary hover:bg-dark text-light focus-visible:ring-primary",
        },
    };

    const styles = variantStyles[variant] ?? variantStyles.danger;

    return (
        /* Backdrop */
        <div
            ref={overlayRef}
            onClick={handleOverlayClick}
            className="fixed inset-0 z-[9999] flex items-center justify-center bg-primary/40 backdrop-blur-sm px-4 animate-in fade-in duration-150"
            role="dialog"
            aria-modal="true"
            aria-labelledby="confirm-dialog-title"
        >
            {/* Panel */}
            <div className="relative w-full max-w-sm bg-white rounded-xl shadow-xl border border-border-gray p-6 animate-in zoom-in-95 duration-150">
                {/* Close × */}
                <button
                    onClick={onClose}
                    className="absolute top-4 right-4 p-1 rounded-lg text-gray hover:text-primary hover:bg-light transition-colors"
                    aria-label="Close"
                >
                    <X className="w-4 h-4" />
                </button>

                {/* Icon */}
                <div
                    className={`inline-flex items-center justify-center w-11 h-11 rounded-full mb-4 ${styles.icon}`}
                >
                    <AlertTriangle className="w-5 h-5" />
                </div>

                {/* Title */}
                <h2
                    id="confirm-dialog-title"
                    className="text-base font-bold text-primary mb-1"
                >
                    {title}
                </h2>

                {/* Message */}
                <p className="text-sm text-gray mb-6 leading-relaxed">
                    {message}
                </p>

                {/* Actions */}
                <div className="flex items-center justify-end gap-3">
                    <button
                        onClick={onClose}
                        className="px-4 py-2 text-sm font-medium border border-border-gray rounded-lg text-primary hover:bg-light transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                    >
                        {cancelLabel}
                    </button>
                    <button
                        onClick={() => {
                            onConfirm();
                            onClose();
                        }}
                        className={`px-4 py-2 text-sm font-medium rounded-lg transition-colors focus-visible:outline-none focus-visible:ring-2 ${styles.button}`}
                    >
                        {confirmLabel}
                    </button>
                </div>
            </div>
        </div>
    );
}
