"use client";

import Image from "next/image";
import { RiSubtractLine, RiAddLine } from "react-icons/ri";
import { CartItem } from "@/types/cart";
import { useState } from "react";

interface CartItemRowProps {
    item: CartItem;
    onUpdateQuantity: (cartId: string, qty: number) => Promise<void>;
    onRemoveItem: (cartId: string) => Promise<void>;
}

export default function CartItemRow({ item, onUpdateQuantity, onRemoveItem }: CartItemRowProps) {
    const [isUpdating, setIsUpdating] = useState(false);

    const handleIncrement = async () => {
        if (isUpdating) return;
        setIsUpdating(true);
        try {
            await onUpdateQuantity(item.id, item.quantity + 1);
        } catch (error) {
            console.error("Failed to increment quantity", error);
        } finally {
            setIsUpdating(false);
        }
    };

    const handleDecrement = async () => {
        if (isUpdating) return;
        setIsUpdating(true);
        try {
            if (item.quantity > 1) {
                await onUpdateQuantity(item.id, item.quantity - 1);
            } else {
                await onRemoveItem(item.id);
            }
        } catch (error) {
            console.error("Failed to decrement quantity", error);
        } finally {
            setIsUpdating(false);
        }
    };

    // Construct format description from attributes or default fallback
    const attributesText = item.attributes && item.attributes.length > 0
        ? item.attributes.map(attr => `${attr.attribute_name}: ${attr.attribute_value}`).join(", ")
        : "Standard Pack";

    const itemPrice = item.product?.final_price ?? 0;
    const subtotal = itemPrice * item.quantity;

    // Use image from API response only
    const imageUrl = item.image || item.product?.image || "";

    return (
        <div className="flex items-start justify-between p-4 border-b border-gray-100 last:border-0 bg-white">
            <div className="flex gap-3">
                <div className="relative w-14 h-14 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 flex-shrink-0">
                    <img
                        src={imageUrl}
                        alt={item.product_name}
                        className="object-cover w-full h-full"
                    />
                </div>

                <div className="space-y-0.5">
                    <p className="text-sm font-semibold text-gray-800 line-clamp-2">
                        {item.product_name}
                    </p>
                    <p className="text-xs text-gray-500 font-medium">
                        {attributesText}
                    </p>
                    <p className="text-xs text-gray-400">
                        ₹{itemPrice} each
                    </p>
                </div>
            </div>

            {/* Counter controls */}
            <div className="flex flex-col items-end gap-2 flex-shrink-0">
                <div className="flex items-center bg-purple-600 text-white rounded-lg overflow-hidden text-xs shadow-sm">
                    <button
                        onClick={handleDecrement}
                        disabled={isUpdating}
                        className="px-2.5 py-1.5 hover:bg-purple-700 active:bg-purple-800 disabled:opacity-50 transition"
                        aria-label="Decrease quantity"
                    >
                        <RiSubtractLine size={14} />
                    </button>

                    <span className="count px-2 font-semibold min-w-[20px] text-center">
                        {item.quantity}
                    </span>

                    <button
                        onClick={handleIncrement}
                        disabled={isUpdating}
                        className="px-2.5 py-1.5 hover:bg-purple-700 active:bg-purple-800 disabled:opacity-50 transition"
                        aria-label="Increase quantity"
                    >
                        <RiAddLine size={14} />
                    </button>
                </div>
                
                <p className="text-sm font-bold text-gray-900">
                    ₹{subtotal}
                </p>
            </div>
        </div>
    );
}
