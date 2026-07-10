import { api } from "@/lib/axios";
import { UserAddressListResponse } from "@/types/address";

export const getUserAddresses = async (): Promise<UserAddressListResponse> => {
    const { data } = await api.get<UserAddressListResponse>("/member/v1/user-addresses");
    return data;
};
