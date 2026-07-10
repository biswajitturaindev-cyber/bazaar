import { useQuery } from "@tanstack/react-query";
import { getUserAddresses } from "@/services/address.service";

export const useUserAddresses = (enabled: boolean = false) => {
    return useQuery({
        queryKey: ["user-addresses"],
        queryFn: getUserAddresses,
        enabled,
    });
};
export default useUserAddresses;
