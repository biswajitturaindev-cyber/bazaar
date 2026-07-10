import { categoryApi } from "@/lib/axios";
import { VendorResponse } from "@/types/vendor";

export const getVendors = async (
    categoryId: string,
    subCategoryId: string
): Promise<VendorResponse> => {
    const { data } = await categoryApi.get<VendorResponse>(
        `/member/vendors?business_category_id=${categoryId}&business_sub_category_id=${subCategoryId}`
    );
    return data;
};
