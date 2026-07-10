import { categoryApi } from "@/lib/axios";
import { BusinessCategoryResponse, BusinessSubCategoryResponse } from "@/types/category";

export const getBusinessCategories = async (): Promise<BusinessCategoryResponse> => {
    const { data } = await categoryApi.get<BusinessCategoryResponse>("/member/business-categories");
    return data;
};

export const getBusinessSubCategories = async (
    categoryId: string,
    search: string = ""
): Promise<BusinessSubCategoryResponse> => {
    const { data } = await categoryApi.get<BusinessSubCategoryResponse>(
        `/member/business-subcategories?business_category_id=${categoryId}&search=${encodeURIComponent(search)}`
    );
    return data;
};
