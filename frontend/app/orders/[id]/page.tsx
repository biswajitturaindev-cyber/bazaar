"use client";

import React, { useState } from "react";
import { useParams, useRouter } from "next/navigation";
import {
    RiArrowLeftLine,
    RiFileList2Line,
    RiMapPinLine,
    RiBankCardLine,
    RiFilePdfLine,
    RiAlertLine,
    RiCheckDoubleLine,
    RiCheckLine,
    RiTruckLine,
    RiBox3Line,
    RiShoppingBagLine,
    RiCloseLine,
} from "react-icons/ri";
import { useOrderDetails } from "@/hooks/order/useOrderDetails";
import { useCancelOrder } from "@/hooks/order/useCancelOrder";
import { useAuthContext } from "@/providers/AuthProvider";
import CancelOrderSheet from "@/components/orders/CancelOrderSheet";
import { categoryApi } from "@/lib/axios";

const STATUS_STEPS = [
    { code: 0, label: "Pending", icon: RiShoppingBagLine },
    { code: 1, label: "Confirmed", icon: RiCheckLine },
    { code: 2, label: "Delivered", icon: RiCheckDoubleLine },
];

export default function OrderDetailsPage() {
    const params = useParams();
    const router = useRouter();
    const orderId = params.id as string;
    const { userId } = useAuthContext();

    // Local states
    const [openCancelSheet, setOpenCancelSheet] = useState(false);
    const [downloadingInvoice, setDownloadingInvoice] = useState(false);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [showPriceDetails, setShowPriceDetails] = useState(true);

    // Queries & Mutations
    const { data: orderDetailsData, isLoading, isError, refetch } = useOrderDetails(orderId);
    const cancelOrderMutation = useCancelOrder(orderId, userId);

    const order = orderDetailsData?.data;

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

    const handleDownloadInvoice = async () => {
        if (downloadingInvoice) return;
        setDownloadingInvoice(true);
        try {
            const { data } = await categoryApi.get(`/member/orders/${orderId}/invoice`);
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

    const handleCancelOrderSubmit = async (reasonId: string, note: string) => {
        try {
            const res = await cancelOrderMutation.mutateAsync({
                order_id: orderId,
                cancel_reason_id: reasonId,
                cancel_note: note,
            });

            if (res.success) {
                setSuccessMessage("Your order has been cancelled successfully.");
                setOpenCancelSheet(false);
                setTimeout(() => setSuccessMessage(null), 5000);
            } else {
                alert(res.message || "Failed to cancel order.");
            }
        } catch (error: any) {
            const errorMsg = error?.response?.data?.message || error?.message || "Something went wrong";
            alert(`Order cancellation failed: ${errorMsg}`);
        }
    };

    // Loading Skeleton
    if (isLoading) {
        return (
            <div className="bg-[#F1F3F6] min-h-screen max-w-3xl mx-auto flex flex-col">
                <header className="py-4 px-4 bg-white border-b border-gray-100 flex items-center gap-4 sticky top-0 z-50">
                    <div className="w-8 h-8 rounded-full bg-gray-200 animate-pulse" />
                    <div className="h-6 w-32 bg-gray-200 rounded animate-pulse" />
                </header>
                <main className="p-3 space-y-3 flex-1">
                    <div className="bg-white rounded-lg p-4 h-20 shadow-sm animate-pulse" />
                    <div className="bg-white rounded-lg p-4 h-56 shadow-sm animate-pulse" />
                    <div className="bg-white rounded-lg p-4 h-32 shadow-sm animate-pulse" />
                </main>
            </div>
        );
    }

    // Error State
    if (isError || !order) {
        return (
            <div className="bg-[#F1F3F6] min-h-screen max-w-3xl mx-auto flex flex-col items-center justify-center p-6 text-center">
                <div className="bg-red-50 text-red-600 p-4 rounded-full mb-4">
                    <RiFileList2Line size={48} />
                </div>
                <h3 className="text-lg font-bold text-gray-800">Order Details Not Found</h3>
                <p className="text-gray-500 text-sm mt-1 mb-6 max-w-xs">
                    We had trouble fetching details for order ID: {orderId}.
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

    // Show cancel button only if order status is PENDING (which maps to numeric value 0)
    const isPending = order.order_status === 0;
    const isCancelled = order.order_status === 5;
    const items = order.items || [];
    const addressDetails = order.addresses?.[0];
    const histories = order.status_histories || [];

    // Build a lookup of when each tracked step actually happened
    const historyByStatus: Record<number, any> = {};
    histories.forEach((h: any) => {
        historyByStatus[h.status] = h;
    });

    const getCurrentStep = (status: number) => {
        switch (status) {
            case 0:
                return 0; // Pending

            default:
                return 2; // Everything else -> Delivered
        }
    };

    const currentStepIndex = isCancelled
        ? -1
        : getCurrentStep(order.order_status);

    return (
        <div className="bg-[#F1F3F6] min-h-screen max-w-3xl mx-auto flex flex-col relative pb-28">
            {/* HEADER */}
            <header className="py-3.5 px-4 bg-white border-b border-gray-100 flex items-center justify-between sticky top-0 z-50">
                <div className="flex items-center gap-3">
                    <button
                        onClick={() => router.back()}
                        className="inline-flex justify-center items-center bg-purple-100 text-purple-700 hover:bg-purple-200 w-8 h-8 rounded-full transition active:scale-90"
                        aria-label="Go back"
                    >
                        <RiArrowLeftLine size={16} />
                    </button>
                    <div>
                        <h3 className="font-bold text-gray-800 text-sm leading-tight">Order Details</h3>
                        <p className="text-[11px] text-gray-400 font-medium leading-tight">{order.order_no}</p>
                    </div>
                </div>

                {!isPending && !isCancelled && (
                    <button
                        onClick={handleDownloadInvoice}
                        disabled={downloadingInvoice}
                        className="flex items-center gap-1 py-1.5 px-3 text-[#2874F0] hover:bg-blue-50 active:scale-95 disabled:opacity-50 text-xs font-bold rounded transition border border-[#2874F0]/30"
                    >
                        <RiFilePdfLine size={15} />
                        {downloadingInvoice ? "Generating..." : "Invoice"}
                    </button>
                )}
            </header>

            <main className="p-3 space-y-3 flex-1">
                {/* SUCCESS NOTIFICATION TOAST */}
                {successMessage && (
                    <div className="bg-green-50 border border-green-200 rounded-lg p-3.5 flex items-start gap-3 text-green-800 text-xs font-semibold">
                        <RiCheckDoubleLine className="text-green-500 shrink-0" size={18} />
                        <p>{successMessage}</p>
                    </div>
                )}

                {/* ITEM PREVIEW STRIP — what you bought, at a glance */}
                <div className="bg-white rounded-lg p-3.5 shadow-sm flex items-center gap-3">
                    <div className="flex -space-x-3">
                        {items.slice(0, 3).map((item, i) => {
                            const itemImg =
                                item.image ||
                                (item.product_snapshot?.image
                                    ? `https://test.resheragroup.in/storage/${item.product_snapshot.image}`
                                    : "");
                            return (
                                <div
                                    key={item.id}
                                    className="w-12 h-12 rounded-lg overflow-hidden bg-gray-50 border-2 border-white shadow-sm"
                                    style={{ zIndex: 3 - i }}
                                >
                                    <img src={itemImg} alt={item.product_name} className="object-cover w-full h-full" />
                                </div>
                            );
                        })}
                    </div>
                    <div className="flex-1 min-w-0">
                        <p className="text-xs font-bold text-gray-800 truncate">
                            {items[0]?.product_name}
                            {items.length > 1 && (
                                <span className="text-gray-400 font-semibold"> +{items.length - 1} more</span>
                            )}
                        </p>
                        <p className="text-[11px] text-gray-400 font-semibold">{order.total_items} items · ₹{order.grand_total}</p>
                    </div>
                </div>

                {/* DELIVERY TRACKING — hero of the page */}
                <div className="bg-white rounded-lg p-4 shadow-sm">
                    {isCancelled ? (
                        <div className="flex items-center gap-3 py-2">
                            <span className="inline-flex items-center justify-center w-9 h-9 rounded-full bg-red-50 text-red-600 shrink-0">
                                <RiCloseLine size={20} />
                            </span>
                            <div>
                                <p className="text-sm font-bold text-red-600">Order Cancelled</p>
                                {order.cancelled_at && (
                                    <p className="text-[11px] text-gray-400 font-semibold">
                                        {formatDate(order.cancelled_at)}
                                    </p>
                                )}
                            </div>
                        </div>
                    ) : (
                        <div className="flex items-start justify-between">
                            {STATUS_STEPS.map((step, idx) => {
                                const isDone = idx <= currentStepIndex;
                                const isCurrent = idx === currentStepIndex;
                                const history = historyByStatus[step.code];
                                const Icon = step.icon;
                                return (
                                    <div key={step.code} className="flex flex-col items-center flex-1 relative">
                                        {idx !== 0 && (
                                            <div
                                                className={`absolute top-4 right-1/2 w-full h-[3px] -z-0 ${
                                                    idx <= currentStepIndex ? "bg-[#388E3C]" : "bg-gray-200"
                                                }`}
                                            />
                                        )}
                                        <span
                                            className={`relative z-10 inline-flex items-center justify-center w-8 h-8 rounded-full border-2 transition-colors ${
                                                isDone
                                                    ? "bg-[#388E3C] border-[#388E3C] text-white"
                                                    : "bg-white border-gray-300 text-gray-300"
                                            } ${isCurrent ? "ring-4 ring-[#388E3C]/15" : ""}`}
                                        >
                                            <Icon size={15} />
                                        </span>
                                        <p
                                            className={`mt-2 text-[10px] font-bold text-center leading-tight ${
                                                isDone ? "text-gray-800" : "text-gray-400"
                                            }`}
                                        >
                                            {step.label}
                                        </p>
                                        {history && (
                                            <p className="text-[9px] text-gray-400 font-semibold text-center mt-0.5">
                                                {formatDate(history.created_at).split(",")[0]}
                                            </p>
                                        )}
                                    </div>
                                );
                            })}
                        </div>
                    )}

                    {!isCancelled && histories.length > 0 && (
                        <p className="text-[11px] text-gray-500 font-semibold mt-4 pt-3 border-t border-gray-100">
                            {histories[histories.length - 1]?.remarks ||
                                `Your order is ${order.order_status_label.toLowerCase()}.`}
                        </p>
                    )}
                </div>

                {/* ITEMS ORDERED */}
                <div className="bg-white rounded-lg shadow-sm overflow-hidden divide-y divide-gray-100">
                    <div className="px-4 py-2.5 bg-gray-50/70">
                        <p className="text-[11px] font-bold text-gray-500">
                            Items in this order ({items.length})
                        </p>
                    </div>
                    {items.map((item) => {
                        const itemImg =
                            item.image ||
                            (item.product_snapshot?.image
                                ? `https://test.resheragroup.in/storage/${item.product_snapshot.image}`
                                : "");

                        const attrText =
                            item.attributes && item.attributes.length > 0
                                ? item.attributes.map((a) => `${a.name}: ${a.value}`).join(", ")
                                : null;

                        return (
                            <div key={item.id} className="p-3.5 flex gap-3 bg-white">
                                <div className="w-16 h-16 rounded-lg overflow-hidden bg-gray-50 border border-gray-100 flex-shrink-0">
                                    <img src={itemImg} alt={item.product_name} className="object-cover w-full h-full" />
                                </div>
                                <div className="flex-1 min-w-0 space-y-1">
                                    <h4 className="text-xs font-semibold text-gray-800 line-clamp-2 leading-snug">
                                        {item.product_name}
                                    </h4>
                                    {attrText && <p className="text-[10px] text-gray-400">{attrText}</p>}
                                    <div className="flex items-center justify-between pt-0.5">
                                        <span className="text-[11px] text-gray-500 font-semibold">
                                            Qty: {item.quantity}
                                        </span>
                                        <span className="text-sm font-bold text-gray-900">₹{item.subtotal}</span>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>

                {/* PRICE DETAILS — expandable, Flipkart-style dashed total */}
                <div className="bg-white rounded-lg shadow-sm overflow-hidden">
                    <button
                        onClick={() => setShowPriceDetails((v) => !v)}
                        className="w-full px-4 py-3 flex items-center justify-between bg-gray-50/70 text-left"
                    >
                        <span className="text-[11px] font-bold text-gray-500">Price Details</span>
                        <span className="text-sm font-bold text-gray-900">₹{order.grand_total}</span>
                    </button>

                    {showPriceDetails && (
                        <div className="p-4 space-y-2.5">
                            <div className="flex justify-between text-xs text-gray-600">
                                <span>Items Total</span>
                                <span>₹{order.items_total}</span>
                            </div>

                            {order.discount_amount > 0 && (
                                <div className="flex justify-between text-xs text-gray-600">
                                    <span>Discounts</span>
                                    <span className="text-green-600">-₹{order.discount_amount}</span>
                                </div>
                            )}

                            <div className="flex justify-between text-xs text-gray-600">
                                <span>Handling Fee</span>
                                <span>₹{order.platform_charge}</span>
                            </div>

                            <div className="flex justify-between text-xs text-gray-600">
                                <span>Delivery Fee</span>
                                <span>
                                    {order.delivery_charge === 0 ? (
                                        <span className="text-green-600 font-bold">FREE</span>
                                    ) : (
                                        `₹${order.delivery_charge}`
                                    )}
                                </span>
                            </div>

                            {order.tax_amount > 0 && (
                                <div className="flex justify-between text-xs text-gray-600">
                                    <span>Tax (GST)</span>
                                    <span>₹{order.tax_amount}</span>
                                </div>
                            )}

                            <div className="border-t border-dashed border-gray-300 pt-2.5 flex justify-between items-center">
                                <span className="text-xs font-bold text-gray-800">Total Amount</span>
                                <span className="text-sm font-bold text-gray-900">₹{order.grand_total}</span>
                            </div>
                        </div>
                    )}
                </div>

                {/* DELIVERY ADDRESS */}
                <div className="bg-white rounded-lg p-4 shadow-sm">
                    <div className="flex gap-3 items-start">
                        <span className="inline-flex justify-center items-center w-8 h-8 text-gray-500 bg-gray-50 rounded-full shrink-0 mt-0.5">
                            <RiMapPinLine size={16} />
                        </span>
                        <div className="space-y-0.5">
                            <p className="text-xs font-bold text-gray-800">Delivery Address</p>
                            <p className="text-xs text-gray-500 leading-relaxed">
                                {addressDetails?.shipping_address || "N/A"}
                            </p>
                            <p className="text-xs text-gray-500 font-semibold pt-0.5">
                                Phone: {addressDetails?.phone || "N/A"}
                            </p>
                        </div>
                    </div>
                </div>

                {/* PAYMENT */}
                <div className="bg-white rounded-lg p-4 shadow-sm">
                    <div className="flex gap-3 items-center">
                        <span className="inline-flex justify-center items-center w-8 h-8 text-gray-500 bg-gray-50 rounded-full shrink-0">
                            <RiBankCardLine size={16} />
                        </span>
                        <div className="flex-1 flex items-center justify-between">
                            <div>
                                <p className="text-xs font-bold text-gray-800">Payment Method</p>
                                <p className="text-[11px] text-gray-400">{order.payment_method_label}</p>
                            </div>
                            <span
                                className={`px-2 py-0.5 font-bold rounded text-[10px] ${
                                    order.payment_status === 1
                                        ? "bg-green-50 text-green-700"
                                        : "bg-amber-50 text-amber-700"
                                }`}
                            >
                                {order.payment_status_label}
                            </span>
                        </div>
                    </div>
                </div>

                {/* CANCELLATION LOG DETAILS */}
                {isCancelled && (
                    <div className="bg-red-50/60 border border-red-100 rounded-lg p-4 space-y-2 text-xs">
                        <div className="flex items-center gap-1.5 text-red-700 font-bold">
                            <RiAlertLine size={15} />
                            <span>Cancellation Details</span>
                        </div>
                        <div className="space-y-1 text-gray-600 font-semibold">
                            <p>
                                Reason:{" "}
                                <span className="text-gray-900">
                                    {order.cancel_note || "Request processed by member"}
                                </span>
                            </p>
                            {order.cancelled_at && (
                                <p>
                                    Cancelled Date: <span className="text-gray-900">{formatDate(order.cancelled_at)}</span>
                                </p>
                            )}
                        </div>
                    </div>
                )}

                {/* ORDER NOTES */}
                {order.notes && (
                    <div className="bg-white rounded-lg p-4 shadow-sm text-xs text-gray-500 leading-relaxed">
                        <p className="text-[11px] font-bold text-gray-500 mb-1">Order Notes</p>
                        {order.notes}
                    </div>
                )}
            </main>

            {/* DOCK BAR - CANCEL ORDER */}
            {isPending && (
                <div className="fixed bottom-0 left-0 right-0 max-w-3xl mx-auto shadow-[0_-8px_30px_rgba(0,0,0,0.06)] bg-white border-t border-gray-100 z-40 p-3 pb-5 flex items-center justify-center">
                    <button
                        onClick={() => setOpenCancelSheet(true)}
                        className="w-full h-12 rounded-xl bg-red-600 hover:bg-red-700 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2 text-sm font-semibold text-white shadow-lg shadow-red-600/20"
                    >
                        <RiAlertLine size={18} />
                        Cancel Order
                    </button>
                </div>
            )}

            {/* CANCEL ORDER SHEET MODAL */}
            <CancelOrderSheet
                open={openCancelSheet}
                onClose={() => setOpenCancelSheet(false)}
                onCancelSubmit={handleCancelOrderSubmit}
                isSubmitting={cancelOrderMutation.isPending}
            />
        </div>
    );
}
