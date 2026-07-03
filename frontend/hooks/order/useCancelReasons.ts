import { useQuery } from "@tanstack/react-query";
import { getCancelReasons } from "@/services/order.service";

export const useCancelReasons = (enabled: boolean = false) => {
    return useQuery({
        queryKey: ["cancel-reasons"],
        queryFn: getCancelReasons,
        enabled,
    });
};
export default useCancelReasons;
