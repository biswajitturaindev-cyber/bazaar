"use client";

import Link from "next/link";
import { Product } from "@/types/product";

interface ProductCardProps {
    product: Product;
}

export default function ProductCard({ product }: ProductCardProps) {
    const productImage = product.image || "https://images.unsplash.com/photo-1542751371-adc38448a05e";

    return (
        <Link
            href={`/product/${product.product_id}?business_id=${product.business.business_id}`}
            className="bg-white rounded-xl p-3 shadow-[0px_2px_10px_0px_rgba(0,0,0,0.06)] hover:shadow-md transition flex flex-col justify-between border border-gray-100 block"
        >
            <div className="w-full aspect-square flex items-center justify-center overflow-hidden rounded-xl bg-gray-50 p-2">
                <img
                    src={productImage}
                    alt={product.name}
                    className="max-h-full max-w-full object-contain rounded-lg"
                />
            </div>
            <div className="flex flex-col mt-2">
                <h3 className="text-[11px] font-medium text-gray-700 line-clamp-2 min-h-[30px] leading-tight">
                    {product.name}
                </h3>
                <div className="flex items-end justify-between mt-2">
                    <span className="text-xs font-bold text-purple-700">₹{product.final_price.toLocaleString()}</span>
                    {product.discount > 0 && (
                        <span className="text-[9px] mb-[1px] text-gray-400 line-through">
                            ₹{product.mrp.toLocaleString()}
                        </span>
                    )}
                </div>
            </div>
        </Link>
    );
}
