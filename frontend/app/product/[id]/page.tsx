"use client";

import { useProductDetailsState } from "@/hooks/product/useProductDetailsState";
import DetailsHeader from "@/components/product-details/DetailsHeader";
import ProductImageGallery from "@/components/product-details/ProductImageGallery";
import ProductInfo from "@/components/product-details/ProductInfo";
import VariantSelector from "@/components/product-details/VariantSelector";
import SpecsTable from "@/components/product-details/SpecsTable";
import LongDescription from "@/components/product-details/LongDescription";
import StickyBottomBar from "@/components/product-details/StickyBottomBar";
import CartBottomPopup from "@/components/product-details/CartBottomPopup";
import { useAuthContext } from "@/providers/AuthProvider";

export default function ProductDetailsPage() {
    const { openLocationPicker } = useAuthContext();
    const {
        setSelectedVariantId,
        quantity,
        handleIncrement,
        handleDecrement,
        productDetails,
        activeVariant,
        activeImage,
        activeImages,
        mrp,
        finalPrice,
        discount,
        isLoading,
        isError,
    } = useProductDetailsState();

    const handleShare = () => {
        if (navigator.share) {
            navigator.share({
                title: productDetails?.name || "Product",
                url: window.location.href,
            }).catch(console.error);
        } else {
            navigator.clipboard.writeText(window.location.href);
            alert("Product link copied to clipboard!");
        }
    };

    if (isLoading) {
        return (
            <main className="mx-auto min-h-screen max-w-3xl bg-white pb-28">
                <DetailsHeader onOpenLocation={() => {}} cartCount={0} />
                <div className="w-full h-72 sm:h-[500px] bg-zinc-200 animate-pulse" />
                <div className="p-5 space-y-4">
                    <div className="h-4 bg-zinc-200 rounded w-1/4 animate-pulse" />
                    <div className="h-6 bg-zinc-200 rounded w-3/4 animate-pulse" />
                    <div className="h-8 bg-zinc-200 rounded w-1/2 animate-pulse" />
                </div>
            </main>
        );
    }

    if (isError || !productDetails) {
        return (
            <main className="mx-auto min-h-screen max-w-3xl bg-white flex flex-col items-center justify-center p-6 text-center">
                <p className="text-red-500 font-semibold mb-4">Failed to load product details.</p>
                <button
                    onClick={() => window.location.reload()}
                    className="px-4 py-2 bg-purple-600 text-white rounded-xl shadow-md hover:bg-purple-700 transition"
                >
                    Retry
                </button>
            </main>
        );
    }

    return (
        <main className="mx-auto min-h-screen max-w-3xl bg-white  relative">
            {/* Header Component */}
            <DetailsHeader
                onOpenLocation={openLocationPicker}
                cartCount={quantity}
            />

            {/* Product Image Gallery Slider */}
            <ProductImageGallery
                key={activeVariant?.variant_id || "default"}
                images={activeImages}
                defaultImage={activeImage}
            />

            {/* Details Content Container */}
            <section className="bg-slate-50 ">
                <div className="p-5 bg-white rounded-t-[2rem] -mt-7 shadow-[0px_-20px_30px_0px_rgba(0,0,0,0.08)] relative pb-26">
                    {/* Basic Info (Title, Category, Timeline, Pricing) */}
                    <ProductInfo
                        name={productDetails.name}
                        categoryName={productDetails.status_label || "Active"}
                        mrp={mrp}
                        finalPrice={finalPrice}
                        discount={discount}
                        shortDescription={activeVariant?.short_description}
                    />

                    {/* Dynamic Variant Selector (Sizes, Colors, Box styles, etc.) */}
                    <VariantSelector
                        variants={productDetails.variants || []}
                        activeVariant={activeVariant}
                        onSelectVariant={setSelectedVariantId}
                    />

                    {/* Specifications Attributes Table */}
                    <SpecsTable
                        attributes={activeVariant?.attributes || []}
                        sku={activeVariant?.sku}
                        barcode={activeVariant?.barcode}
                        totalStock={activeVariant?.total_stock}
                        statusLabel={productDetails.status_label}
                    />

                    <LongDescription
                        htmlContent={activeVariant?.long_description}
                        shortDescription={activeVariant?.short_description}
                    />
                </div>
            </section>

            {/* Sticky Action Bottom Bar */}
            <StickyBottomBar
                quantity={quantity}
                onIncrement={handleIncrement}
                onDecrement={handleDecrement}
                onShare={handleShare}
            />

            {/* View Cart Bottom Drawer Overlay */}
            <CartBottomPopup
                itemCount={quantity}
                show={quantity > 0}
            />

        </main>
    );
}
