"use client";

import React, { useState } from "react";
import { useRouter } from "next/navigation";
import { 
    RiCalendarLine, 
    RiFileList2Line, 
    RiCoinsLine, 
    RiFilePdfLine,
    RiArrowDownSLine,
    RiArrowUpSLine
} from "react-icons/ri";
import { Order } from "@/types/order";
import { categoryApi } from "@/lib/axios";

interface OrderCardProps {
    order: Order;
}

export default function OrderCard({ order }: OrderCardProps) {
    const router = useRouter();
    const [expanded, setExpanded] = useState(false);
    const [downloadingInvoice, setDownloadingInvoice] = useState(false);

    // Dynamic badge styles based on order status
    const getStatusStyle = (status: string) => {
        const lower = status.toLowerCase();
        if (lower.includes("deliver")) {
            return "bg-green-50 text-green-700 border-green-100";
        }
        if (lower.includes("cancel") || lower.includes("fail")) {
            return "bg-red-50 text-red-700 border-red-100";
        }
        if (lower.includes("process") || lower.includes("ship")) {
            return "bg-blue-50 text-blue-700 border-blue-100";
        }
        return "bg-amber-50 text-amber-700 border-amber-100";
    };

    // Format placed date
    const formatDate = (dateStr: string) => {
        try {
            const date = new Date(dateStr);
            return date.toLocaleDateString("en-IN", {
                day: "numeric",
                month: "short",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            });
        } catch (e) {
            return dateStr;
        }
    };

    const handleDownloadInvoice = async (e: React.MouseEvent) => {
        e.stopPropagation();
        if (downloadingInvoice) return;
        
        setDownloadingInvoice(true);
        try {
            const { data } = await categoryApi.get(`/member/orders/${order.id}/invoice`);
            if (data.success && data.invoice_url) {
                window.open(data.invoice_url, "_blank");
            } else {
                alert(data.message || "Failed to load invoice url.");
            }
        } catch (err: any) {
            console.error(err);
            alert("Unable to fetch invoice PDF. Please try again later.");
        } finally {
            setDownloadingInvoice(false);
        }
    };

    const items = order.items || [];
    const displayedItems = expanded ? items : items.slice(0, 1);
    const hasMoreItems = items.length > 1;

    return (
        <div 
            onClick={() => router.push(`/orders/${order.id}`)}
            className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-50 hover:shadow-md transition-shadow duration-200 cursor-pointer"
        >
            {/* CARD HEADER */}
            <div className="p-4 flex items-start justify-between gap-3">
                <div className="space-y-1">
                    <div className="flex items-center gap-2">
                        <span className="text-xs font-bold text-gray-400 uppercase tracking-wider">
                            Order No
                        </span>
                        <span className="text-xs font-bold text-purple-700">
                            {order.order_no}
                        </span>
                    </div>

                    <div className="flex items-center gap-1.5 text-xs text-gray-500 font-medium">
                        <RiCalendarLine size={13} className="text-gray-400" />
                        {formatDate(order.placed_at || order.created_at)}
                    </div>
                </div>

                <span className={`px-2.5 py-1 text-[11px] font-bold rounded-full border ${getStatusStyle(order.order_status_label)}`}>
                    {order.order_status_label}
                </span>
            </div>

            {/* ORDER ITEMS LIST */}
            <div className="p-4 space-y-3.5 bg-gray-50/20">
                {displayedItems.map((item) => {
                    const itemImg = item.image || (item.product_snapshot && item.product_snapshot.image 
                        ? `https://test.resheragroup.in/storage/${item.product_snapshot.image}` 
                        : "");
                    
                    const attrText = item.attributes && item.attributes.length > 0
                        ? item.attributes.map(a => `${a.name}: ${a.value}`).join(", ")
                        : null;

                    return (
                        <div key={item.id} className="flex gap-3 items-start">
                            <div className="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 relative">
                                <img
                                    src={itemImg}
                                    alt={item.product_name}
                                    className="object-cover w-full h-full"
                                />
                            </div>

                            <div className="flex-1 min-w-0 space-y-0.5">
                                <h4 className="text-xs font-bold text-gray-800 line-clamp-1">
                                    {item.product_name}
                                </h4>
                                {attrText && (
                                    <p className="text-[10px] text-gray-400 font-medium truncate">
                                        {attrText}
                                    </p>
                                )}
                                <p className="text-[10px] text-gray-500 font-semibold">
                                    Qty: {item.quantity} × ₹{item.final_price}
                                </p>
                            </div>

                            <p className="text-xs font-bold text-gray-900 flex-shrink-0">
                                ₹{item.subtotal}
                            </p>
                        </div>
                    );
                })}

                {/* EXPAND / COLLAPSE BUTTON */}
                {hasMoreItems && (
                    <button
                        onClick={(e) => {
                            e.stopPropagation();
                            setExpanded(!expanded);
                        }}
                        className="w-full text-center text-[11px] font-bold text-purple-600 flex items-center justify-center gap-1 py-1 hover:text-purple-700 transition"
                    >
                        {expanded ? (
                            <>
                                Show Less <RiArrowUpSLine size={14} />
                            </>
                        ) : (
                            <>
                                View +{items.length - 1} More Item{items.length - 1 > 1 ? "s" : ""} <RiArrowDownSLine size={14} />
                            </>
                        )}
                    </button>
                )}
            </div>

            {/* CARD FOOTER / PRICE DETAILS */}
            <div className="p-4 flex flex-col sm:flex-row justify-between gap-3 text-xs">
                <div className="flex items-center gap-4 text-gray-600 font-semibold">
                    <span className="flex items-center gap-1">
                        <RiFileList2Line size={14} className="text-gray-400" />
                        Items: {order.total_items}
                    </span>
                    <span className="flex items-center gap-1">
                        <RiCoinsLine size={14} className="text-gray-400" />
                        {order.payment_method_label}
                    </span>
                </div>

                <div className="flex items-center justify-between sm:justify-end gap-6 pt-2 sm:pt-0 border-t sm:border-0 border-gray-100">
                    <div className="text-right">
                        <p className="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Grand Total</p>
                        <p className="text-sm font-bold text-purple-700">₹{order.grand_total}</p>
                    </div>

                    {order.order_status !== 0 && order.order_status !== 5 && (
                        <button
                            onClick={handleDownloadInvoice}
                            disabled={downloadingInvoice}
                            className="flex items-center gap-1 py-1.5 px-3 bg-purple-50 text-purple-700 hover:bg-purple-100 active:scale-95 disabled:opacity-50 text-[11px] font-bold rounded-lg transition"
                        >
                            <RiFilePdfLine size={14} />
                            {downloadingInvoice ? "Generating..." : "Invoice"}
                        </button>
                    )}
                </div>
            </div>
        </div>
    );
}
