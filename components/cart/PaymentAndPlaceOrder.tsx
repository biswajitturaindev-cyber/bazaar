"use client";

import { useState } from "react";
import { 
    RiArrowRightSLine, 
    RiWallet3Line, 
    RiCoinsLine, 
    RiBankCardLine,
    RiArrowUpSLine
} from "react-icons/ri";

interface PaymentAndPlaceOrderProps {
    grandTotal: number;
    paymentMethod: "WALLET" | "ONLINE" | "COD";
    onChangePaymentMethod: (method: "WALLET" | "ONLINE" | "COD") => void;
    onPlaceOrder: () => void;
    isPlacingOrder: boolean;
    disabled?: boolean;
}

export default function PaymentAndPlaceOrder({
    grandTotal,
    paymentMethod,
    onChangePaymentMethod,
    onPlaceOrder,
    isPlacingOrder,
    disabled = false,
}: PaymentAndPlaceOrderProps) {
    const [showSelector, setShowSelector] = useState(false);

    const getPaymentDetails = (method: "WALLET" | "ONLINE" | "COD") => {
        switch (method) {
            case "WALLET":
                return {
                    label: "Bazaar Wallet",
                    description: "Pay using wallet balance",
                    icon: <RiWallet3Line className="text-purple-600" size={20} />,
                };
            case "ONLINE":
                return {
                    label: "BHIM UPI / Cards / NetBanking",
                    description: "Pay securely online",
                    icon: <RiBankCardLine className="text-purple-600" size={20} />,
                };
            case "COD":
            default:
                return {
                    label: "Cash On Delivery (COD)",
                    description: "Pay in cash at delivery time",
                    icon: <RiCoinsLine className="text-purple-600" size={20} />,
                };
        }
    };

    const activeDetails = getPaymentDetails(paymentMethod);

    const selectMethod = (method: "WALLET" | "ONLINE" | "COD") => {
        onChangePaymentMethod(method);
        setShowSelector(false);
    };

    return (
        <>
            <div className="fixed bottom-0 left-0 right-0 max-w-3xl mx-auto shadow-[0_-8px_30px_rgba(0,0,0,0.08)] bg-white/95 backdrop-blur-md border-t border-gray-100 z-40 p-4 pb-6 flex items-center justify-between gap-4">
                {/* Payment Selector Trigger */}
                <button
                    onClick={() => setShowSelector(true)}
                    className="flex flex-col text-left space-y-0.5 max-w-[50%] select-none active:opacity-75 transition duration-150"
                    aria-haspopup="dialog"
                >
                    <p className="text-[10px] text-gray-400 font-bold uppercase tracking-wider flex items-center gap-1">
                        Pay Using
                        <RiArrowUpSLine size={12} className="text-gray-400" />
                    </p>
                    <div className="flex items-center gap-1.5 min-w-0">
                        {activeDetails.icon}
                        <p className="font-bold text-xs text-gray-800 truncate">
                            {paymentMethod === "ONLINE" ? "BHIM UPI / Cards" : activeDetails.label}
                        </p>
                    </div>
                </button>

                {/* Place Order Button */}
                <button
                    onClick={onPlaceOrder}
                    disabled={isPlacingOrder || disabled}
                    className="bg-purple-700 hover:bg-purple-800 active:scale-[0.98] disabled:bg-purple-400 disabled:scale-100 text-white rounded-xl px-5 py-3 flex items-center gap-6 shadow-md transition-all duration-200"
                >
                    <div className="text-left leading-tight pr-4 border-r border-white/20">
                        <p className="text-sm font-bold">₹{grandTotal}</p>
                        <p className="text-[10px] opacity-80 uppercase font-bold tracking-wider">Total</p>
                    </div>

                    <span className="flex items-center gap-1 font-semibold text-sm">
                        {isPlacingOrder ? "Placing Order..." : "Place Order"}
                        {!isPlacingOrder && <RiArrowRightSLine size={18} />}
                    </span>
                </button>
            </div>

            {/* Bottom Sheet Modal for Payment Selection */}
            {showSelector && (
                <>
                    <div
                        onClick={() => setShowSelector(false)}
                        className="fixed inset-0 bg-black/45 backdrop-blur-sm z-[99998] transition-opacity duration-300"
                    />
                    <div className="fixed left-0 bottom-0 z-[99999] w-full rounded-t-3xl bg-white shadow-2xl transition-transform duration-300 translate-y-0 max-w-3xl left-1/2 -translate-x-1/2">
                        <div className="p-5 pt-6 pb-8">
                            <div className="flex justify-between items-center mb-6 pb-2 border-b border-gray-100">
                                <h3 className="text-base font-bold text-gray-900">
                                    Select Payment Method
                                </h3>
                                <button
                                    onClick={() => setShowSelector(false)}
                                    className="text-xs font-semibold text-gray-400 hover:text-gray-600 transition"
                                >
                                    Cancel
                                </button>
                            </div>

                            <div className="space-y-2.5">
                                {(["ONLINE", "COD", "WALLET"] as const).map((method) => {
                                    const details = getPaymentDetails(method);
                                    const isSelected = paymentMethod === method;
                                    return (
                                        <div
                                            key={method}
                                            onClick={() => selectMethod(method)}
                                            className={`flex items-center justify-between p-4 rounded-xl cursor-pointer border transition-all duration-150 ${
                                                isSelected
                                                    ? "bg-purple-50/50 border-purple-500/50"
                                                    : "bg-gray-50/50 border-gray-200/50 hover:bg-gray-100/50"
                                            }`}
                                        >
                                            <div className="flex items-center gap-3">
                                                <div className={`p-2 rounded-lg ${isSelected ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-500'}`}>
                                                    {details.icon}
                                                </div>
                                                <div>
                                                    <p className={`font-bold text-xs ${isSelected ? "text-purple-900" : "text-gray-800"}`}>
                                                        {details.label}
                                                    </p>
                                                    <p className="text-[11px] text-gray-500 mt-0.5">
                                                        {details.description}
                                                    </p>
                                                </div>
                                            </div>
                                            <div className={`w-4 h-4 rounded-full border-2 flex items-center justify-center ${
                                                isSelected ? "border-purple-600 bg-purple-600" : "border-gray-300"
                                            }`}>
                                                {isSelected && <div className="w-1.5 h-1.5 rounded-full bg-white" />}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    </div>
                </>
            )}
        </>
    );
}
