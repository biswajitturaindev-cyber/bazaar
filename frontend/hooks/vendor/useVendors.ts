import { useQuery } from "@tanstack/react-query";
import { getVendors } from "@/services/vendor.service";

export const useVendors = (categoryId: string, subCategoryId: string) => {
    return useQuery({
        queryKey: ["vendors", categoryId, subCategoryId],
        queryFn: () => getVendors(categoryId, subCategoryId),
        enabled: !!categoryId && !!subCategoryId,
    });
};
