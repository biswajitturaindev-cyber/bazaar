import { useQuery } from "@tanstack/react-query";
import { getOrderDetails } from "@/services/order.service";

export const useOrderDetails = (orderId: string) => {
    return useQuery({
        queryKey: ["order-details", orderId],
        queryFn: () => getOrderDetails(orderId),
        enabled: !!orderId,
    });
};
export default useOrderDetails;
