"use client";

import { useState } from "react";
import { ProductImage } from "@/types/product";

interface ProductImageGalleryProps {
    images: ProductImage[];
    defaultImage: string;
}

export default function ProductImageGallery({
    images,
    defaultImage,
}: ProductImageGalleryProps) {
    const [selectedImageIndex, setSelectedImageIndex] = useState<number>(0);

    const imageUrls = images.map((img) => img.image_large || img.image_medium || defaultImage);
    const activeUrl = imageUrls[selectedImageIndex] || defaultImage;

    return (
        <section className="relative bg-white flex flex-col items-center">
            {/* Primary Display */}
            <div className="w-full h-72 sm:h-[500px] overflow-hidden">
                <img
                    src={activeUrl}
                    alt="Product"
                    className="w-full h-full object-cover transition-all duration-300"
                />
            </div>

            {/* Thumbnail Indicator Dots / Micro Gallery */}
            {imageUrls.length > 1 && (
                <div className="absolute bottom-12 flex gap-2 bg-black/30 px-3 py-1.5 rounded-full backdrop-blur-sm">
                    {imageUrls.map((_, index) => (
                        <button
                            key={index}
                            onClick={() => setSelectedImageIndex(index)}
                            className={`w-2.5 h-2.5 rounded-full transition ${
                                index === selectedImageIndex ? "bg-white scale-110" : "bg-white/50 hover:bg-white/70"
                            }`}
                        />
                    ))}
                </div>
            )}
        </section>
    );
}
