import { useQuery } from "@tanstack/react-query";
import { getBusinessCategories } from "@/services/category.service";

export const useBusinessCategories = () => {
    return useQuery({
        queryKey: ["business-categories"],
        queryFn: getBusinessCategories,
        staleTime: 1000 * 60 * 60 * 24, // 24 hours
        gcTime: 1000 * 60 * 60 * 24,    // 24 hours
    });
};
