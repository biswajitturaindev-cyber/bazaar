"use client";

import React from "react";
import {
    RiCloseLine,
    RiSearchLine,
    RiFocus3Line,
    RiArrowRightSLine,
    RiHome4Line,
} from "react-icons/ri";
import { useUserAddresses } from "@/hooks/address/useUserAddresses";
import { UserAddress } from "@/types/address";
import { formatAddress } from "@/utils/address";

interface LocationSheetProps {
    open: boolean;
    onClose: () => void;
    onSelectAddress: (address: UserAddress) => void;
    selectedAddressId?: string | number | null;
    preventClose?: boolean;
}

export default function LocationSheet({
    open,
    onClose,
    onSelectAddress,
    selectedAddressId,
    preventClose = false
}: LocationSheetProps) {
    const { data: addressesData, isLoading, isError } = useUserAddresses(open);

    const rawData = addressesData as any;
    let addresses: UserAddress[] = [];
    if (rawData?.data?.shipping && Array.isArray(rawData.data.shipping)) {
        addresses = rawData.data.shipping;
    } else if (rawData?.shipping && Array.isArray(rawData.shipping)) {
        addresses = rawData.shipping;
    } else if (Array.isArray(rawData)) {
        addresses = rawData;
    } else if (rawData?.data && Array.isArray(rawData.data)) {
        addresses = rawData.data;
    }

    return (
        <>
            {/* Overlay (clicking outside closes the modal) */}
            <div
                onClick={onClose}
                className={`fixed inset-0 z-[99998] bg-black/40 backdrop-blur-sm transition-all duration-300 ${
                    open ? "opacity-100" : "opacity-0 pointer-events-none"
                }`}
            />

            {/* Bottom Sheet */}
            <div
                className={`fixed left-0 bottom-0 z-[99999] w-full rounded-t-3xl bg-white shadow-2xl transition-transform duration-300 max-w-3xl left-1/2 -translate-x-1/2 ${
                    open ? "translate-y-0" : "translate-y-full"
                }`}
            >
                <div className="max-h-[80vh] overflow-y-auto p-5 pt-6 pb-8 no-scrollbar">
                    {/* Header with Title and Close Button */}
                    <div className="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
                        <h2 className="text-lg font-semibold text-gray-900">
                            Select delivery location
                        </h2>
                        {!preventClose && (
                            <button
                                onClick={onClose}
                                className="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 text-purple-700 hover:bg-purple-200 transition active:scale-95"
                                aria-label="Close location selector"
                            >
                                <RiCloseLine size={20} />
                            </button>
                        )}
                    </div>

                    {/* Search */}
                    <div className="mb-5 flex items-center gap-2 rounded-full bg-gray-100 px-4 py-3">
                        <RiSearchLine className="text-gray-400" />
                        <input
                            placeholder="Search for area, street name..."
                            className="w-full bg-transparent text-sm outline-none text-gray-800 placeholder-gray-400"
                        />
                    </div>

                    {/* Current Location (GPS) */}
                    <div className="space-y-2 mb-6">
                        <div className="flex cursor-pointer items-center justify-between rounded-xl bg-gray-50 p-4 transition hover:bg-gray-100 active:scale-[0.99]">
                            <div className="flex items-center gap-3">
                                <RiFocus3Line className="text-purple-600" size={22} />
                                <div>
                                    <p className="font-semibold text-purple-600">
                                        Use your current location
                                    </p>
                                </div>
                            </div>
                            <RiArrowRightSLine className="text-gray-400" size={22} />
                        </div>
                    </div>

                    {/* Saved Addresses Section Title */}
                    <p className="mb-3 mt-6 text-sm text-gray-500">
                        Your saved addresses
                    </p>

                    {/* Addresses List Container */}
                    <div className="space-y-5">
                        {isLoading && (
                            <div className="space-y-4">
                                {[1, 2].map(n => (
                                    <div key={n} className="flex gap-3 animate-pulse">
                                        <div className="w-10 h-10 bg-purple-100 rounded-lg shrink-0" />
                                        <div className="flex-1 space-y-2">
                                            <div className="h-4 bg-gray-100 rounded w-1/4" />
                                            <div className="h-3 bg-gray-100 rounded w-3/4" />
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}

                        {isError && (
                            <div className="p-4 bg-red-50 border border-red-100 rounded-xl text-center text-xs text-red-650 font-semibold">
                                Failed to load saved addresses. Please try again.
                            </div>
                        )}

                        {!isLoading && !isError && addresses.length === 0 && (
                            <div className="p-6 text-center text-xs text-gray-400 font-semibold border border-dashed border-gray-200 rounded-xl">
                                No saved addresses found.
                            </div>
                        )}

                        {!isLoading && !isError && addresses.map((addr) => {
                            const isSelected = selectedAddressId === addr.id;
                            const displayName = addr.name || addr.full_name || "Recipient";
                            const displayAddressText = formatAddress(addr);

                            return (
                                <div
                                    key={addr.id}
                                    onClick={() => onSelectAddress(addr)}
                                    className={`flex gap-3 cursor-pointer p-2.5 rounded-xl transition ${
                                        isSelected
                                            ? "bg-purple-50/60 border border-purple-200/50"
                                            : "hover:bg-gray-50 border border-transparent"
                                    }`}
                                >
                                    <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-200 shrink-0">
                                        <RiHome4Line className="text-purple-700" size={20} />
                                    </div>

                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center gap-2">
                                            <p className="font-semibold text-gray-800 capitalize">
                                                {addr.type || "address"}
                                            </p>
                                            <span className="text-xs text-purple-705 font-bold truncate">
                                                {displayName} ({addr.phone || "N/A"})
                                            </span>
                                            {addr.is_default && (
                                                <span className="text-[9px] font-extrabold bg-green-50 text-green-700 px-1.5 py-0.5 rounded border border-green-150">
                                                    DEFAULT
                                                </span>
                                            )}
                                        </div>
                                        <p className="mt-1 text-sm leading-snug text-gray-500">
                                            {displayAddressText}
                                        </p>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </div>
        </>
    );
}
