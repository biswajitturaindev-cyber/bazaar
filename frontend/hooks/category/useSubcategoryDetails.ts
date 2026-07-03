import { useState, useMemo } from "react";
import { useParams } from "next/navigation";
import { useVendors } from "@/hooks/vendor/useVendors";
import { useBusinessSubCategories } from "@/hooks/category/useBusinessSubCategories";
import { useProducts } from "@/hooks/product/useProducts";
import { FilterState } from "@/components/category/FilterSheet";

export const useSubcategoryDetails = () => {
    const params = useParams();
    const categoryId = params.id as string;
    const subCategoryId = params.subCategoryId as string;

    const [openLocation, setOpenLocation] = useState(false);
    const [openFilter, setOpenFilter] = useState(false);
    const [selectedVendorId, setSelectedVendorId] = useState<string | null>(null);
    const [activeFilters, setActiveFilters] = useState<FilterState | null>(null);

    // Queries
    const { data: vendorsData, isLoading: isVendorsLoading } = useVendors(categoryId, subCategoryId);
    const { data: subcategoriesData } = useBusinessSubCategories(categoryId);

    const vendors = vendorsData?.data || [];
    const subcategories = subcategoriesData?.data || [];

    // Dynamically resolve active vendor ID
    const activeVendorId = selectedVendorId || vendors[0]?.id || null;

    // Fetch products dynamically based on active vendor ID
    const { data: productsData, isLoading: isProductsLoading } = useProducts(activeVendorId);

    // Find current subcategory name
    const currentSubcategory = subcategories.find((sub) => sub.id === subCategoryId);
    const subcategoryTitle = currentSubcategory?.name || "Subcategory";

    const categoryName = subcategories[0]?.category?.name || "";
    const isFashion = categoryName.toLowerCase().includes("fashion") || categoryId === "wMvbmOeYAl";

    // Dynamic Filter & Sort Logic (Functional Filters)
    const filteredProducts = useMemo(() => {
        const rawProducts = productsData?.data || [];
        let result = [...rawProducts];

        if (!activeFilters) return result;

        const { sortBy, categories, sizes, priceRange, color, fabrics } = activeFilters;

        // 1. Filter by category names
        if (categories && categories.length > 0) {
            result = result.filter((product) =>
                categories.some((cat) => product.name.toLowerCase().includes(cat.toLowerCase()))
            );
        }

        // 2. Filter by sizes
        if (sizes && sizes.length > 0) {
            result = result.filter((product) => {
                const variant = product.primary_variant;
                if (!variant) return false;
                return variant.attributes.some(
                    (attr) =>
                        attr.attribute_name.toLowerCase() === "size" &&
                        sizes.some((s) => attr.value.toLowerCase() === s.toLowerCase())
                );
            });
        }

        // 3. Filter by price range
        if (priceRange.min) {
            const min = parseFloat(priceRange.min);
            if (!isNaN(min)) {
                result = result.filter((product) => product.final_price >= min);
            }
        }
        if (priceRange.max) {
            const max = parseFloat(priceRange.max);
            if (!isNaN(max)) {
                result = result.filter((product) => product.final_price <= max);
            }
        }

        // 4. Filter by color
        if (color) {
            result = result.filter((product) => {
                const variant = product.primary_variant;
                if (!variant) return false;
                return variant.attributes.some(
                    (attr) =>
                        attr.attribute_name.toLowerCase() === "color" &&
                        attr.value.toLowerCase().includes(color.toLowerCase())
                );
            });
        }

        // 5. Filter by fabric
        if (fabrics && fabrics.length > 0) {
            result = result.filter((product) => {
                const variant = product.primary_variant;
                if (!variant) return false;
                return variant.attributes.some(
                    (attr) =>
                        attr.attribute_name.toLowerCase() === "fabric" &&
                        fabrics.some((f) => attr.value.toLowerCase() === f.toLowerCase())
                );
            });
        }

        // 6. Sort By
        if (sortBy === "newest") {
            result.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
        } else if (sortBy === "price_low_high") {
            result.sort((a, b) => a.final_price - b.final_price);
        } else if (sortBy === "price_high_low") {
            result.sort((a, b) => b.final_price - a.final_price);
        }

        return result;
    }, [productsData?.data, activeFilters]);

    return {
        categoryId,
        subCategoryId,
        openLocation,
        setOpenLocation,
        openFilter,
        setOpenFilter,
        selectedVendorId,
        setSelectedVendorId,
        activeVendorId,
        vendors,
        isVendorsLoading,
        subcategories,
        subcategoryTitle,
        categoryName,
        isFashion,
        products: filteredProducts,
        isProductsLoading,
        setActiveFilters,
    };
};
