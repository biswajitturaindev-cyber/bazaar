import { useQuery } from "@tanstack/react-query";
import { getBusinessSubCategories } from "@/services/category.service";

export const useBusinessSubCategories = (categoryId: string, search: string = "") => {
    return useQuery({
        queryKey: ["business-subcategories", categoryId, search],
        queryFn: () => getBusinessSubCategories(categoryId, search),
        enabled: !!categoryId,
    });
};
