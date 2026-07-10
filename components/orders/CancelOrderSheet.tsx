"use client";

import React, { useState } from "react";
import { RiCloseLine, RiAlertLine } from "react-icons/ri";
import { useCancelReasons } from "@/hooks/order/useCancelReasons";

interface CancelOrderSheetProps {
    open: boolean;
    onClose: () => void;
    onCancelSubmit: (reasonId: string, note: string) => Promise<void>;
    isSubmitting: boolean;
}

export default function CancelOrderSheet({
    open,
    onClose,
    onCancelSubmit,
    isSubmitting,
}: CancelOrderSheetProps) {
    const [selectedReasonId, setSelectedReasonId] = useState<string | null>(null);
    const [cancelNote, setCancelNote] = useState("");

    // Fetch cancellation reasons from server only when sheet is opened
    const { data: reasonsData, isLoading, isError } = useCancelReasons(open);

    const reasons = reasonsData?.data || [];

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!selectedReasonId || isSubmitting) return;
        await onCancelSubmit(selectedReasonId, cancelNote);
    };

    return (
        <>
            {/* Backdrop Overlay */}
            <div
                onClick={onClose}
                className={`fixed inset-0 z-[99998] bg-black/45 backdrop-blur-sm transition-all duration-300 ${
                    open ? "opacity-100" : "opacity-0 pointer-events-none"
                }`}
            />

            {/* Bottom Sheet Modal */}
            <div
                className={`fixed left-0 bottom-0 z-[99999] w-full rounded-t-[2rem] bg-white shadow-2xl transition-transform duration-300 max-w-3xl left-1/2 -translate-x-1/2 ${
                    open ? "translate-y-0" : "translate-y-full"
                }`}
            >
                <div className="max-h-[85vh] overflow-y-auto p-5 pt-6 pb-8">
                    {/* Header */}
                    <div className="flex justify-between items-center mb-5 pb-2 border-b border-gray-100">
                        <div className="flex items-center gap-2 text-red-600">
                            <RiAlertLine size={20} />
                            <h3 className="text-base font-bold text-gray-900">
                                Cancel Order
                            </h3>
                        </div>
                        <button
                            onClick={onClose}
                            className="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition active:scale-95"
                            aria-label="Close cancel sheet"
                        >
                            <RiCloseLine size={20} />
                        </button>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-5">
                        <p className="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Please select a reason for cancellation:
                        </p>

                        {/* Reasons Loading Skeleton */}
                        {isLoading && (
                            <div className="space-y-2.5">
                                {[1, 2, 3].map(n => (
                                    <div key={n} className="h-12 bg-gray-100 rounded-xl animate-pulse" />
                                ))}
                            </div>
                        )}

                        {/* Error State */}
                        {isError && (
                            <div className="p-4 bg-red-50 border border-red-100 rounded-xl text-center text-xs text-red-600 font-medium">
                                Failed to load cancellation reasons. Please try again.
                            </div>
                        )}

                        {/* Reasons List */}
                        {!isLoading && !isError && (
                            <div className="space-y-2 max-h-[30vh] overflow-y-auto no-scrollbar">
                                {reasons.map((item) => {
                                    const isSelected = selectedReasonId === item.id;
                                    return (
                                        <div
                                            key={item.id}
                                            onClick={() => setSelectedReasonId(item.id)}
                                            className={`flex items-center justify-between p-3.5 rounded-xl cursor-pointer border transition-all duration-150 ${
                                                isSelected
                                                    ? "bg-red-50/40 border-red-500/40"
                                                    : "bg-gray-50/50 border-gray-200/50 hover:bg-gray-100/50"
                                            }`}
                                        >
                                            <span className={`text-xs font-semibold ${isSelected ? "text-red-900 font-bold" : "text-gray-700"}`}>
                                                {item.reason}
                                            </span>
                                            <div className={`w-4.5 h-4.5 rounded-full border-2 flex items-center justify-center ${
                                                isSelected ? "border-red-600 bg-red-600" : "border-gray-300"
                                            }`}>
                                                {isSelected && <div className="w-1.5 h-1.5 rounded-full bg-white" />}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        )}

                        {/* Cancellation Note */}
                        <div className="space-y-1.5">
                            <label className="text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Additional Notes (Optional)
                            </label>
                            <textarea
                                placeholder="Explain reason for cancellation..."
                                value={cancelNote}
                                onChange={(e) => setCancelNote(e.target.value)}
                                rows={3}
                                className="w-full bg-gray-50 border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-500/25 outline-none rounded-xl px-4 py-2.5 text-xs text-gray-800 placeholder-gray-400 transition"
                            />
                        </div>

                        {/* Action Buttons */}
                        <div className="flex gap-3 pt-2">
                            <button
                                type="button"
                                onClick={onClose}
                                className="flex-1 py-3 bg-gray-100 hover:bg-gray-200 active:scale-[0.98] text-gray-700 font-bold rounded-xl transition text-xs text-center"
                            >
                                Keep Order
                            </button>
                            <button
                                type="submit"
                                disabled={!selectedReasonId || isSubmitting}
                                className="flex-1 py-3 bg-red-600 hover:bg-red-700 active:scale-[0.98] disabled:bg-red-300 disabled:scale-100 text-white font-bold rounded-xl transition text-xs shadow-md shadow-red-100 flex items-center justify-center gap-1"
                            >
                                {isSubmitting ? "Cancelling..." : "Cancel Order"}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}
