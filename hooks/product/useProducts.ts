import { useQuery } from "@tanstack/react-query";
import { getProducts } from "@/services/product.service";

export const useProducts = (businessId: string | null) => {
    return useQuery({
        queryKey: ["products", businessId],
        queryFn: () => getProducts(businessId!),
        enabled: !!businessId,
    });
};
