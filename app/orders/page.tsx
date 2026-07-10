"use client";

import React, { useState } from "react";
import { useRouter } from "next/navigation";
import {
    RiArrowLeftLine,
    RiArrowLeftSLine,
    RiArrowRightSLine,
    RiInboxArchiveLine,
} from "react-icons/ri";
import { useOrders } from "@/hooks/order/useOrders";
import OrderList from "@/components/orders/OrderList";
import { useAuthContext } from "@/providers/AuthProvider";

export default function MyOrdersPage() {
    const router = useRouter();
    const { userId } = useAuthContext();
    const [page, setPage] = useState(1);

    // Queries
    const { data: ordersData, isLoading, isError, refetch } = useOrders(userId!, page);

    const orders = ordersData?.data || [];
    const meta = ordersData?.meta;
    const lastPage = meta?.last_page || 1;

    const handlePrevPage = () => {
        if (page > 1) {
            setPage(page - 1);
        }
    };

    const handleNextPage = () => {
        if (page < lastPage) {
            setPage(page + 1);
        }
    };

    // Skeleton Loader state
    if (isLoading) {
        return (
            <div className="bg-[#F1F3F6] min-h-screen max-w-3xl mx-auto flex flex-col">
                <header className="py-3.5 px-4 bg-white border-b border-gray-100 flex items-center gap-3 sticky top-0 z-50">
                    <div className="w-8 h-8 rounded-full bg-gray-200 animate-pulse" />
                    <div className="h-5 w-28 bg-gray-200 rounded animate-pulse" />
                </header>
                <main className="p-3 space-y-3 flex-1">
                    {[1, 2, 3].map((n) => (
                        <div key={n} className="bg-white rounded-lg p-4 space-y-3 shadow-sm animate-pulse h-40" />
                    ))}
                </main>
            </div>
        );
    }

    // Error State
    if (isError) {
        return (
            <div className="bg-[#F1F3F6] min-h-screen max-w-3xl mx-auto flex flex-col items-center justify-center p-6 text-center">
                <div className="bg-red-50 text-red-600 p-4 rounded-full mb-4">
                    <RiInboxArchiveLine size={48} />
                </div>
                <h3 className="text-lg font-bold text-gray-800">Unable to load orders</h3>
                <p className="text-gray-500 text-sm mt-1 mb-6 max-w-xs">
                    We had trouble fetching your order history. Please try again.
                </p>
                <button
                    onClick={() => refetch()}
                    className="px-6 py-2.5 bg-[#2874F0] hover:bg-[#1d5ec2] text-white rounded font-semibold shadow-md transition active:scale-95 text-sm"
                >
                    Try Again
                </button>
            </div>
        );
    }

    // Empty State
    if (!orders.length) {
        return (
            <div className="bg-[#F1F3F6] min-h-screen max-w-3xl mx-auto flex flex-col">
                <header className="py-3.5 px-4 bg-white border-b border-gray-100 flex items-center gap-3 sticky top-0 z-50">
                    <button
                        onClick={() => router.back()}
                        className="inline-flex justify-center items-center bg-purple-100 text-purple-700 hover:bg-purple-200 w-8 h-8 rounded-full transition active:scale-90"
                        aria-label="Go back"
                    >
                        <RiArrowLeftLine size={16} />
                    </button>
                    <h3 className="font-bold text-gray-800 text-sm">My Orders</h3>
                </header>
                <div className="flex-1 flex flex-col items-center justify-center p-6 text-center">
                    <div className="bg-white text-gray-300 p-5 rounded-full mb-4 shadow-sm">
                        <RiInboxArchiveLine size={44} />
                    </div>
                    <h3 className="text-sm font-bold text-gray-800">No orders yet</h3>
                    <p className="text-gray-400 text-xs mt-1 max-w-xs">
                        Items you order will show up here so you can track and manage them.
                    </p>
                </div>
            </div>
        );
    }

    return (
        <div className="bg-[#F1F3F6] min-h-screen max-w-3xl mx-auto flex flex-col relative pb-10">
            {/* HEADER */}
            <header className="py-3.5 px-4 bg-white border-b border-gray-100 flex items-center gap-3 sticky top-0 z-50">
                <button
                    onClick={() => router.back()}
                    className="inline-flex justify-center items-center bg-purple-100 text-purple-700 hover:bg-purple-200 w-8 h-8 rounded-full transition active:scale-90"
                    aria-label="Go back"
                >
                    <RiArrowLeftLine size={16} />
                </button>
                <h3 className="font-bold text-gray-800 text-sm">My Orders</h3>
            </header>

            {/* ORDERS CONTAINER */}
            <main className="p-3 flex-1 flex flex-col space-y-3">
                <OrderList orders={orders} />

                {/* PAGINATION CONTROLS */}
                {lastPage > 1 && (
                    <div className="flex items-center justify-between bg-white rounded-lg p-3 shadow-sm mt-1">
                        <button
                            onClick={handlePrevPage}
                            disabled={page === 1}
                            className="inline-flex items-center gap-1 py-1.5 px-3 hover:bg-gray-50 active:scale-95 disabled:opacity-40 disabled:scale-100 text-xs font-bold text-gray-700 rounded transition"
                        >
                            <RiArrowLeftSLine size={16} /> Previous
                        </button>

                        <span className="text-xs font-bold text-gray-400">
                            Page {page} of {lastPage}
                        </span>

                        <button
                            onClick={handleNextPage}
                            disabled={page === lastPage}
                            className="inline-flex items-center gap-1 py-1.5 px-3 hover:bg-gray-50 active:scale-95 disabled:opacity-40 disabled:scale-100 text-xs font-bold text-[#2874F0] rounded transition"
                        >
                            Next <RiArrowRightSLine size={16} />
                        </button>
                    </div>
                )}
            </main>
        </div>
    );
}
