"use client";

interface ProductInfoProps {
    name: string;
    categoryName?: string;
    mrp: number;
    finalPrice: number;
    discount: number;
    shortDescription?: string | null;
}

export default function ProductInfo({
    name,
    categoryName = "Women Collection",
    mrp,
    finalPrice,
    discount,
    shortDescription,
}: ProductInfoProps) {
    // Calculate display discount if percentage is zero but mrp differs
    const displayDiscount = discount > 0 ? discount : mrp > finalPrice ? Math.round(((mrp - finalPrice) / mrp) * 100) : 0;

    // Dynamically calculate estimated delivery date (3 days from today)
    const getDeliveryDateString = () => {
        const date = new Date();
        date.setDate(date.getDate() + 3);
        return date.toLocaleDateString("en-US", {
            weekday: "long",
            day: "numeric",
            month: "short",
            year: "numeric",
        });
    };

    return (
        <div className="border-b border-slate-100 pb-4">
            <p className="text-xs text-purple-600 font-semibold mb-1.5 uppercase tracking-wide">
                {categoryName}
            </p>
            <h1 className="text-xl font-bold text-gray-900 leading-snug my-1">
                {name}
            </h1>

            {/* Dynamic Delivery Timeline info */}
            <div className="flex items-center gap-1.5 text-gray-500 mt-2 text-xs">
                <span className="w-1.5 h-1.5 bg-purple-500 rounded-full animate-pulse"></span>
                <span>Delivery by {getDeliveryDateString()}</span>
            </div>

            {/* Price Details */}
            <div className="pt-4 flex items-center gap-3">
                <p className="text-2xl font-extrabold text-gray-900">₹{finalPrice.toLocaleString()}</p>
                {mrp > finalPrice && (
                    <>
                        <p className="text-base text-gray-400 line-through">₹{mrp.toLocaleString()}</p>
                        {displayDiscount > 0 && (
                            <span className="bg-red-50 text-red-600 text-[10px] font-bold px-2 py-1 rounded-full">
                                {displayDiscount}% OFF
                            </span>
                        )}
                    </>
                )}
            </div>

            {/* Short Description */}
            {shortDescription && (
                <p className="text-sm text-gray-500 mt-4 leading-relaxed font-normal">
                    {shortDescription}
                </p>
            )}
        </div>
    );
}
