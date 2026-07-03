import { categoryApi } from "@/lib/axios";
import { ProductResponse, ProductDetailsResponse } from "@/types/product";

export const getProducts = async (businessId: string): Promise<ProductResponse> => {
    const { data } = await categoryApi.get<ProductResponse>(
        `/member/products?business_id=${businessId}`
    );
    return data;
};

export const getProductDetails = async (
    productId: string,
    businessId: string
): Promise<ProductDetailsResponse> => {
    const { data } = await categoryApi.get<ProductDetailsResponse>(
        `/member/products/details?product_id=${productId}&business_id=${businessId}`
    );
    return data;
};
