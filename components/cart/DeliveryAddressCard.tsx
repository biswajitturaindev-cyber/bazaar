"use client";

import { RiHome4Line } from "react-icons/ri";

interface DeliveryAddressCardProps {
    address: string;
    onOpenChangeAddress: () => void;
}

export default function DeliveryAddressCard({
    address,
    onOpenChangeAddress,
}: DeliveryAddressCardProps) {
    return (
        <div className="bg-white/95 backdrop-blur-md px-4 py-3 flex justify-between items-center shadow-sm border-b border-gray-100">
            <div className="flex items-center gap-3 flex-1 min-w-0 mr-4">
                <span className="inline-flex justify-center items-center text-lg w-9 h-9 font-medium text-white bg-purple-100 shrink-0 rounded-full">
                    <RiHome4Line className="text-purple-600" size={18} />
                </span>

                <div className="space-y-0.5 min-w-0">
                    <p className="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">
                        Delivering to <span className="text-gray-900 font-bold">Home</span>
                    </p>
                    <p className="text-xs font-semibold text-gray-700 truncate">
                        {address}
                    </p>
                </div>
            </div>

            <button
                onClick={onOpenChangeAddress}
                className="text-green-600 text-xs font-bold bg-green-50 px-3 py-1.5 rounded-full hover:bg-green-100 transition active:scale-95 flex-shrink-0"
            >
                Change
            </button>
        </div>
    );
}
