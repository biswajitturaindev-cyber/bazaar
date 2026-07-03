import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import {
    getCart,
    addToCart,
    updateCartItem,
    removeCartItem,
    clearCart,
} from "@/services/cart.service";
import { AddToCartPayload } from "@/types/cart";

export const useCart = (userId: number) => {
    return useQuery({
        queryKey: ["cart", userId],
        queryFn: () => getCart(userId),
        enabled: !!userId,
    });
};

export const useAddToCart = (userId: number) => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (payload: AddToCartPayload) => addToCart(payload),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ["cart", userId] });
        },
    });
};

export const useUpdateCart = (userId: number) => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: ({ cartId, quantity }: { cartId: string; quantity: number }) =>
            updateCartItem(cartId, quantity),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ["cart", userId] });
        },
    });
};

export const useRemoveCartItem = (userId: number) => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (cartId: string) => removeCartItem(cartId),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ["cart", userId] });
        },
    });
};

export const useClearCart = (userId: number) => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: () => clearCart(userId),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ["cart", userId] });
        },
    });
};
