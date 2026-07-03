"use client";

import Link from "next/link";
import { Product } from "@/types/product";

interface FashionProductCardProps {
    product: Product;
}

export default function FashionProductCard({ product }: FashionProductCardProps) {
    const productImage = product.image || "https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=300&q=80";
    
    // Fallback description for fashion product subtitle
    const description = product.primary_variant?.short_description || "Daily Ethnic Wear";

    return (
        <Link
            href={`/product/${product.product_id}?business_id=${product.business.business_id}`}
            className="block rounded-[14px] overflow-hidden group cursor-pointer"
        >
            <div className="overflow-hidden rounded-[14px] bg-[#f3f3f3] flex items-center justify-center aspect-[3/4] relative">
                <img
                    src={productImage}
                    alt={product.name}
                    className="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                />
            </div>
            <div className="pt-2 px-1">
                <h3 className="text-[15px] truncate leading-tight font-medium text-black group-hover:text-purple-700 transition">
                    {product.name}
                </h3>
                <p className="text-xs text-[#7a7a7a] mt-1 truncate">
                    {description}
                </p>
                <p className="text-sm font-semibold text-black mt-1.5">
                    ₹{product.final_price.toLocaleString()}
                </p>
            </div>
        </Link>
    );
}
