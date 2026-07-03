import { useMutation, useQueryClient } from "@tanstack/react-query";
import { cancelOrder } from "@/services/order.service";
import { CancelOrderPayload } from "@/types/order";

export const useCancelOrder = (orderId: string, userId?: number | null) => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (payload: CancelOrderPayload) => cancelOrder(payload),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ["order-details", orderId] });
            if (userId) {
                queryClient.invalidateQueries({ queryKey: ["orders", userId] });
            }
        },
    });
};
export default useCancelOrder;
