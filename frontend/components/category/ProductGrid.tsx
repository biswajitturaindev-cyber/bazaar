"use client";

import { Product } from "@/types/product";
import ProductCard from "./ProductCard";

interface ProductGridProps {
    products: Product[];
    isLoading: boolean;
    activeVendorId: string | null;
    subcategoryTitle: string;
}

export default function ProductGrid({
    products,
    isLoading,
    activeVendorId,
    subcategoryTitle,
}: ProductGridProps) {
    return (
        <section className="px-4 mt-4">
            {/* Section Title */}
            <div className="flex justify-between items-center mb-3">
                <h3 className="text-base font-semibold text-gray-900">Top Deals in {subcategoryTitle}</h3>
                <span className="text-xs text-purple-600 font-semibold cursor-pointer hover:underline">View all</span>
            </div>

            {/* Grid */}
            <div className="grid grid-cols-2 sm:grid-cols-3 gap-x-3 gap-y-4">
                {isLoading ? (
                    [1, 2, 3].map((n) => (
                        <div
                            key={n}
                            className="bg-white rounded-xl p-3 shadow-sm border border-gray-100 flex flex-col justify-between h-48 animate-pulse"
                        >
                            <div className="w-full aspect-square bg-zinc-200 rounded-lg" />
                            <div className="h-4 bg-zinc-200 rounded w-3/4 mt-2" />
                            <div className="h-4 bg-zinc-200 rounded w-1/2 mt-2" />
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
                        <ProductCard key={product.product_id} product={product} />
                    ))
                )}
            </div>
        </section>
    );
}
