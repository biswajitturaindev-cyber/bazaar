import { useQuery } from "@tanstack/react-query";
import { getOrders } from "@/services/order.service";

export const useOrders = (userId: number, page: number = 1) => {
    return useQuery({
        queryKey: ["orders", userId, page],
        queryFn: () => getOrders(userId, page),
        enabled: !!userId,
    });
};
