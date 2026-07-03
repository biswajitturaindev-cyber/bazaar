import { useMutation, useQueryClient } from "@tanstack/react-query";
import { placeOrder } from "@/services/order.service";
import { PlaceOrderPayload } from "@/types/order";

export const usePlaceOrder = (userId: number) => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (payload: PlaceOrderPayload) => placeOrder(payload),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ["cart", userId] });
        },
    });
};
