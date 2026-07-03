import { categoryApi } from "@/lib/axios";
import {
    CartResponse,
    AddToCartPayload,
    AddToCartResponse,
    UpdateCartItemResponse,
    RemoveCartItemResponse,
    ClearCartResponse,
} from "@/types/cart";

export const getCart = async (userId: number): Promise<CartResponse> => {
    const { data } = await categoryApi.get<CartResponse>(`/member/cart?user_id=${userId}`);
    return data;
};

export const addToCart = async (payload: AddToCartPayload): Promise<AddToCartResponse> => {
    const { data } = await categoryApi.post<AddToCartResponse>("/member/cart", payload);
    return data;
};

export const updateCartItem = async (
    cartId: string,
    quantity: number
): Promise<UpdateCartItemResponse> => {
    const { data } = await categoryApi.post(
        `/member/cart/${cartId}`,
        {
            quantity,
        },
        {
            headers: {
                "X-HTTP-Method-Override": "PUT",
            },
        }
    );

    return data;
};

export const removeCartItem = async (cartId: string): Promise<RemoveCartItemResponse> => {
    const { data } = await categoryApi.delete<RemoveCartItemResponse>(`/member/cart/${cartId}`);
    return data;
};

export const clearCart = async (userId: number): Promise<ClearCartResponse> => {
    const { data } = await categoryApi.delete<ClearCartResponse>(`/member/cart/user/${userId}`);
    return data;
};
