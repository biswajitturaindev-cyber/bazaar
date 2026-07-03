import { useState } from "react";
import { useParams, useSearchParams } from "next/navigation";
import { useProductDetails } from "@/hooks/product/useProductDetails";
import { useCart, useAddToCart, useUpdateCart, useRemoveCartItem } from "@/hooks/cart/useCart";

import { useAuthContext } from "@/providers/AuthProvider";

export const useProductDetailsState = () => {
    const params = useParams();
    const searchParams = useSearchParams();

    const productId = params.id as string;
    const businessId = searchParams.get("business_id") || "";

    const [openLocation, setOpenLocation] = useState(false);
    const [selectedVariantId, setSelectedVariantId] = useState<string | null>(null);

    const { data: detailsData, isLoading, isError } = useProductDetails(productId, businessId);
    const productDetails = detailsData?.data;

    // Cart integrations
    const { userId } = useAuthContext();
    const { data: cartData } = useCart(userId!);
    const addToCartMutation = useAddToCart(userId!);
    const updateCartMutation = useUpdateCart(userId!);
    const removeCartItemMutation = useRemoveCartItem(userId!);

    // Dynamically resolve active variant ID (avoids useEffect setState cascading renders)
    const primaryVariant = productDetails?.variants?.find((v) => v.is_primary);
    const activeVariantId = selectedVariantId || primaryVariant?.variant_id || productDetails?.variants?.[0]?.variant_id || null;

    // Active variant resolution based on resolved ID
    const activeVariant = productDetails?.variants?.find((v) => v.variant_id === activeVariantId) || null;

    // Derived states
    const activeImage = activeVariant?.images?.[0]?.image_medium || productDetails?.image || "";
    const activeImages = activeVariant?.images || [];
    
    const mrp = activeVariant?.mrp || productDetails?.mrp || 0;
    const finalPrice = activeVariant?.final_price || productDetails?.final_price || 0;
    const discount = activeVariant?.discount || productDetails?.discount || 0;

    // Calculate current item quantity in cart
    const cartItem = cartData?.data?.find(
        (item) => item.product_id === productId && item.product_variant_id === activeVariantId
    );
    const quantity = cartItem ? cartItem.quantity : 0;

    const isCartMutating = addToCartMutation.isPending || updateCartMutation.isPending || removeCartItemMutation.isPending;

    const handleIncrement = async () => {
        if (isCartMutating || !productDetails) return;

        if (quantity === 0) {
            const payload = {
                user_id: userId!,
                business_id: productDetails.business.business_id,
                business_category_id: productDetails.business_category_id,
                product_id: productDetails.product_id,
                product_variant_id: activeVariantId,
                quantity: 1,
                attributes: activeVariant?.attributes?.map((attr) => ({
                    attribute_master_id: attr.attribute_id,
                    attribute_value_id: attr.value_id,
                })) || null,
            };
            await addToCartMutation.mutateAsync(payload);
        } else if (cartItem) {
            await updateCartMutation.mutateAsync({
                cartId: cartItem.id,
                quantity: quantity + 1,
            });
        }
    };

    const handleDecrement = async () => {
        if (isCartMutating || !cartItem) return;

        if (quantity > 1) {
            await updateCartMutation.mutateAsync({
                cartId: cartItem.id,
                quantity: quantity - 1,
            });
        } else {
            await removeCartItemMutation.mutateAsync(cartItem.id);
        }
    };

    return {
        productId,
        businessId,
        openLocation,
        setOpenLocation,
        selectedVariantId: activeVariantId,
        setSelectedVariantId,
        quantity,
        handleIncrement,
        handleDecrement,
        isCartMutating,
        productDetails,
        activeVariant,
        activeImage,
        activeImages,
        mrp,
        finalPrice,
        discount,
        isLoading,
        isError,
    };
};
