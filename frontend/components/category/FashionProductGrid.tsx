"use client";

import { RiFilter3Line } from "react-icons/ri";
import { Product } from "@/types/product";
import FashionProductCard from "./FashionProductCard";

interface FashionProductGridProps {
    products: Product[];
    isLoading: boolean;
    activeVendorId: string | null;
    onOpenFilter: () => void;
}

export default function FashionProductGrid({
    products,
    isLoading,
    activeVendorId,
    onOpenFilter,
}: FashionProductGridProps) {
    return (
        <section className="px-4 mt-6 bg-white max-w-3xl mx-auto">
            {/* Header with Filter Button */}
            <div className="flex mb-4 items-center justify-between">
                <h2 className="text-lg font-semibold text-gray-900">Popular Products</h2>
                <button
                    onClick={onOpenFilter}
                    className="px-3 text-[13px] bg-slate-100 text-purple-700 font-medium border border-purple-200 py-1.5 rounded-lg flex items-center gap-1 hover:bg-slate-200 transition"
                >
                    <RiFilter3Line size={16} />
                    Filter
                </button>
            </div>

            {/* Grid */}
            <div className="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-6">
                {isLoading ? (
                    [1, 2, 3].map((n) => (
                        <div key={n} className="block rounded-[14px] animate-pulse">
                            <div className="rounded-[14px] bg-zinc-200 aspect-[3/4]" />
                            <div className="h-4 bg-zinc-200 rounded w-3/4 mt-2" />
                            <div className="h-3 bg-zinc-200 rounded w-1/2 mt-1" />
                            <div className="h-4 bg-zinc-200 rounded w-1/3 mt-2" />
                        </div>
                    ))
                ) : !activeVendorId ? (
                    <div className="col-span-full rounded-2xl border border-zinc-200 bg-white p-8 text-center text-xs text-gray-500">
                        Please select a vendor to view products.
                    </div>
                ) : products.length === 0 ? (
                    <div className="col-span-full rounded-2xl border border-zinc-200 bg-white p-8 text-center text-xs text-gray-500">
                        No products found for this vendor.
                    </div>
                ) : (
                    products.map((product) => (
                        <FashionProductCard key={product.product_id} product={product} />
                    ))
                )}
            </div>
        </section>
    );
}
