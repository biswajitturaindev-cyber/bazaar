import { useQuery } from "@tanstack/react-query";
import { getProductDetails } from "@/services/product.service";

export const useProductDetails = (productId: string | null, businessId: string | null) => {
    return useQuery({
        queryKey: ["product-details", productId, businessId],
        queryFn: () => getProductDetails(productId!, businessId!),
        enabled: !!productId && !!businessId,
    });
};
