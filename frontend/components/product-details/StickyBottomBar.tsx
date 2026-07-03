"use client";

import { RiShareForward2Fill, RiSubtractLine, RiAddLine } from "react-icons/ri";

interface StickyBottomBarProps {
    quantity: number;
    onIncrement: () => void;
    onDecrement: () => void;
    onShare: () => void;
}

export default function StickyBottomBar({
    quantity,
    onIncrement,
    onDecrement,
    onShare,
}: StickyBottomBarProps) {
    return (
        <div className="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-100 px-4 py-3 flex items-center gap-3 shadow-[0_-8px_30px_rgba(0,0,0,0.08)] z-40 max-w-3xl mx-auto">
            {/* Share Button */}
            <button
                onClick={onShare}
                className="w-12 h-12 rounded-xl border border-gray-200 flex items-center justify-center text-gray-700 hover:bg-gray-50 transition"
            >
                <RiShareForward2Fill size={20} />
            </button>

            {/* Buy Now / Counter Button */}
            <div className="flex-1">
                {quantity <= 0 ? (
                    <button
                        onClick={onIncrement}
                        className="w-full bg-purple-600 hover:bg-purple-700 active:scale-[0.99] text-white py-3.5 rounded-2xl font-semibold transition shadow-md"
                    >
                        Buy Now
                    </button>
                ) : (
                    <div className="w-full bg-purple-600 rounded-2xl py-2 flex justify-center shadow-md">
                        <div className="flex items-center justify-between w-full px-5 bg-purple-600 text-white rounded-lg overflow-hidden">
                            <button
                                onClick={onDecrement}
                                className="px-4 py-1.5 hover:bg-purple-700 transition rounded"
                            >
                                <RiSubtractLine size={20} />
                            </button>

                            <span className="px-3 text-xl font-semibold">{quantity}</span>

                            <button
                                onClick={onIncrement}
                                className="px-4 py-1.5 hover:bg-purple-700 transition rounded"
                            >
                                <RiAddLine size={20} />
                            </button>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
