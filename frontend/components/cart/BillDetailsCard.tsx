"use client";

import { RiFileListLine, RiShoppingBagLine, RiMotorbikeLine } from "react-icons/ri";

interface BillDetailsCardProps {
    itemsTotal: number;
    handlingCharge: number;
    deliveryCharge: number;
    freeDeliveryThreshold?: number;
    itemCount: number;
}

export default function BillDetailsCard({
    itemsTotal,
    handlingCharge,
    deliveryCharge,
    freeDeliveryThreshold = typeof process !== "undefined" && process.env.NEXT_PUBLIC_FREE_DELIVERY_THRESHOLD ? Number(process.env.NEXT_PUBLIC_FREE_DELIVERY_THRESHOLD) : 150,
    itemCount,
}: BillDetailsCardProps) {
    
    // Determine if delivery is free
    const isFreeDelivery = itemsTotal >= freeDeliveryThreshold;
    const actualDeliveryCharge = isFreeDelivery ? 0 : deliveryCharge;
    const grandTotal = itemsTotal + handlingCharge + actualDeliveryCharge;
    const amountNeededForFreeDelivery = freeDeliveryThreshold - itemsTotal;

    return (
        <div className="bg-white rounded-2xl p-4 shadow-sm space-y-3.5 border border-gray-50">
            <h2 className="font-semibold text-gray-900 text-sm">Bill details</h2>

            {/* Items Total */}
            <div className="flex justify-between text-xs text-gray-600">
                <div className="flex items-center gap-2">
                    <RiFileListLine className="text-gray-400" size={16} />
                    Items total
                </div>
                <div className="font-medium text-gray-900">
                    ₹{itemsTotal}
                </div>
            </div>

            {/* Handling Charge */}
            <div className="flex justify-between text-xs text-gray-600">
                <div className="flex items-center gap-2">
                    <RiShoppingBagLine className="text-gray-400" size={16} />
                    Handling charge
                </div>
                <div className="font-medium text-gray-900">
                    ₹{handlingCharge}
                </div>
            </div>

            {/* Delivery Charge */}
            <div className="flex justify-between text-xs text-gray-600">
                <div className="flex items-center gap-2">
                    <RiMotorbikeLine className="text-gray-400" size={16} />
                    Delivery charge
                </div>
                <div className="font-medium text-gray-900">
                    {isFreeDelivery ? (
                        <div className="flex items-center gap-1.5">
                            <span className="line-through text-gray-400">₹{deliveryCharge}</span>
                            <span className="text-green-600 font-semibold">FREE</span>
                        </div>
                    ) : (
                        `₹${deliveryCharge}`
                    )}
                </div>
            </div>

            {/* Free Delivery Promo Message */}
            {!isFreeDelivery && amountNeededForFreeDelivery > 0 && (
                <p className="text-orange-500 text-[11px] font-medium bg-orange-50/50 px-2.5 py-1.5 rounded-lg border border-orange-100/50">
                    Shop for ₹{amountNeededForFreeDelivery} more to get FREE delivery
                </p>
            )}

            {isFreeDelivery && (
                <p className="text-green-600 text-[11px] font-medium bg-green-50/50 px-2.5 py-1.5 rounded-lg border border-green-100/50">
                    🎉 Free delivery applied to this order!
                </p>
            )}

            <hr className="border-gray-100" />

            {/* Total Row */}
            <div className="flex justify-between text-sm items-center pt-0.5">
                <div className="font-semibold text-gray-700">
                    Items - {itemCount.toString().padStart(2, "0")}
                </div>
                <div className="text-purple-700 font-bold text-base flex items-baseline gap-1.5">
                    <span className="text-xs text-gray-400 font-medium">Grand total</span>
                    ₹{grandTotal}
                </div>
            </div>
        </div>
    );
}
