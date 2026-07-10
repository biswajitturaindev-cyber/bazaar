"use client";

import React from "react";
import { useRouter } from "next/navigation";
import { RiInboxArchiveLine } from "react-icons/ri";
import { Order } from "@/types/order";
import OrderCard from "./OrderCard";

interface OrderListProps {
    orders: Order[];
}

export default function OrderList({ orders }: OrderListProps) {
    const router = useRouter();

    if (orders.length === 0) {
        return (
            <div className="flex-1 flex flex-col items-center justify-center p-8 text-center bg-white rounded-3xl border border-gray-100/50 shadow-sm min-h-[350px]">
                <div className="w-16 h-16 bg-purple-50 text-purple-500 rounded-full flex items-center justify-center mb-5 border border-purple-100">
                    <RiInboxArchiveLine size={28} />
                </div>
                <h3 className="text-base font-bold text-gray-800">No Orders Found</h3>
                <p className="text-gray-500 text-xs mt-1.5 mb-6 max-w-xs leading-relaxed">
                    You haven't placed any orders with Bazaar yet. Browse our selection and place your first order!
                </p>
                <button
                    onClick={() => router.push("/home")}
                    className="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 active:scale-[0.98] text-white rounded-xl font-bold shadow-md transition text-xs"
                >
                    Start Shopping
                </button>
            </div>
        );
    }

    return (
        <div className="space-y-4">
            {orders.map((order) => (
                <OrderCard key={order.id} order={order} />
            ))}
        </div>
    );
}
