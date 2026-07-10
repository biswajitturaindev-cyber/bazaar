"use client";

import { Vendor } from "@/types/vendor";

interface VendorScrollProps {
    vendors: Vendor[];
    isLoading: boolean;
    activeVendorId: string | null;
    onSelectVendor: (id: string) => void;
}

export default function VendorScroll({
    vendors,
    isLoading,
    activeVendorId,
    onSelectVendor,
}: VendorScrollProps) {
    return (
        <section className="px-4 py-3">
            <h3 className="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Available Vendors</h3>
            {isLoading ? (
                <div className="flex gap-3 overflow-x-auto pb-2">
                    {[1, 2, 3].map((n) => (
                        <div key={n} className="h-[90px] min-w-[140px] rounded-xl bg-zinc-200 animate-pulse" />
                    ))}
                </div>
            ) : vendors.length === 0 ? (
                <div className="rounded-xl border border-dashed border-gray-300 bg-white p-6 text-center text-xs text-gray-500">
                    No vendors found for this subcategory.
                </div>
            ) : (
                <div className="flex gap-3 overflow-x-auto pb-2 no-scrollbar scroll-smooth">
                    {vendors.map((vendor) => {
                        const isSelected = activeVendorId === vendor.id;
                        const shopPhoto = vendor.kycdetail?.shop_photo?.url || "https://images.unsplash.com/photo-1542751371-adc38448a05e";
                        const logoPhoto = vendor.kycdetail?.owner_photo?.url || "https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe";
                        return (
                            <button
                                key={vendor.id}
                                onClick={() => onSelectVendor(vendor.id)}
                                className={`vendor relative h-[90px] min-w-[140px] overflow-hidden rounded-xl border border-gray-200 transition ${
                                    isSelected ? "ring-2 ring-purple-600 scale-[0.98]" : "hover:border-gray-300"
                                }`}
                            >
                                <img src={shopPhoto} alt="" className="absolute inset-0 h-full w-full object-cover" />
                                <span className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent block" />
                                <span className="absolute inset-0 flex -translate-y-2 items-center justify-center">
                                    <span className="h-10 w-10 overflow-hidden rounded-full border border-white bg-white shadow block">
                                        <img src={logoPhoto} alt="" className="h-full w-full object-cover aspect-square" />
                                    </span>
                                </span>
                                <span className="absolute bottom-2 left-2 right-2 text-left text-xs font-semibold text-white truncate">
                                    {vendor.business_name}
                                </span>
                            </button>
                        );
                    })}
                </div>
            )}
        </section>
    );
}
