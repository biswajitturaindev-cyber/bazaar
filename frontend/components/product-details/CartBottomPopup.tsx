"use client";

import Link from "next/link";
import { RiShoppingBasketLine, RiArrowRightSLine } from "react-icons/ri";

interface CartBottomPopupProps {
    itemCount: number;
    show: boolean;
}

export default function CartBottomPopup({
    itemCount,
    show,
}: CartBottomPopupProps) {
    return (
        <div
            className={`fixed bottom-20 left-1/2 -translate-x-1/2 z-40 bg-purple-600 text-white px-4 py-3 rounded-full flex items-center gap-4 shadow-xl w-[240px] transition-all duration-500 ${
                show ? "translate-y-0 opacity-100 scale-100" : "translate-y-24 opacity-0 scale-95 pointer-events-none"
            }`}
        >
            {/* Basket icon */}
            <span className="inline-flex justify-center items-center text-lg w-10 h-10 font-medium text-white bg-purple-500 shrink-0 rounded-full">
                <RiShoppingBasketLine size={20} />
            </span>

            <div className="flex-1">
                <p className="font-semibold text-sm">View Cart</p>
                <p className="text-[13px] opacity-85">{itemCount} Item{itemCount > 1 ? "s" : ""}</p>
            </div>

            {/* Checkout Link button */}
            <Link
                href="/cart"
                className="w-10 h-10 bg-white text-black rounded-full flex items-center justify-center shadow hover:bg-slate-50 transition active:scale-95"
            >
                <RiArrowRightSLine size={24} />
            </Link>
        </div>
    );
}
