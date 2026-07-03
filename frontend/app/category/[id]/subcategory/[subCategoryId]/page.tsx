"use client";

import { useSubcategoryDetails } from "@/hooks/category/useSubcategoryDetails";
import DetailHeader from "@/components/category/DetailHeader";
import VendorScroll from "@/components/category/VendorScroll";
import SubcategoryScroll from "@/components/category/SubcategoryScroll";
import OfferBanner from "@/components/category/OfferBanner";
import ProductGrid from "@/components/category/ProductGrid";
import FashionBanner from "@/components/category/FashionBanner";
import FashionProductGrid from "@/components/category/FashionProductGrid";
import FilterSheet, { FilterState } from "@/components/category/FilterSheet";
import { useAuthContext } from "@/providers/AuthProvider";

export default function SubcategoryDetailPage() {
    const {
        categoryId,
        subCategoryId,
        openLocation,
        setOpenLocation,
        openFilter,
        setOpenFilter,
        setSelectedVendorId,
        activeVendorId,
        vendors,
        isVendorsLoading,
        subcategories,
        subcategoryTitle,
        isFashion,
        products,
        isProductsLoading,
        setActiveFilters,
    } = useSubcategoryDetails();
    const { openLocationPicker } = useAuthContext();

    const handleApplyFilters = (filters: FilterState) => {
        setActiveFilters(filters);
    };

    return (
        <main className={`mx-auto min-h-screen max-w-3xl pb-20 ${isFashion ? "bg-white" : "bg-slate-50"}`}>
            {/* Header Component */}
            <DetailHeader
                categoryId={categoryId}
                onOpenLocation={openLocationPicker}
                onOpenFilter={() => setOpenFilter(true)}
            />

            {/* Vendor Banner Scroll Component */}
            <VendorScroll
                vendors={vendors}
                isLoading={isVendorsLoading}
                activeVendorId={activeVendorId}
                onSelectVendor={setSelectedVendorId}
            />

            {isFashion ? (
                <>
                    {/* Fashion Layout Variation */}
                    <FashionBanner />

                    {/* Subcategories Horizontal Scroll under categories header */}
                    <section className="p-4">
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
                        <SubcategoryScroll
                            categoryId={categoryId}
                            subCategoryId={subCategoryId}
                            subcategories={subcategories}
                        />
                    </section>

                    {/* Fashion Product Grid */}
                    <FashionProductGrid
                        products={products}
                        isLoading={isProductsLoading}
                        activeVendorId={activeVendorId}
                        onOpenFilter={() => setOpenFilter(true)}
                    />
                </>
            ) : (
                <>
                    {/* Electronics / Default Layout Variation */}
                    <SubcategoryScroll
                        categoryId={categoryId}
                        subCategoryId={subCategoryId}
                        subcategories={subcategories}
                    />

                    <OfferBanner />

                    <ProductGrid
                        products={products}
                        isLoading={isProductsLoading}
                        activeVendorId={activeVendorId}
                        subcategoryTitle={subcategoryTitle}
                    />
                </>
            )}

            {/* Filters Bottom Sheet */}
            <FilterSheet open={openFilter} onClose={() => setOpenFilter(false)} onApply={handleApplyFilters} />
        </main>
    );
}
