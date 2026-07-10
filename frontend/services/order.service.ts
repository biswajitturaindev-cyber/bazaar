import { categoryApi } from "@/lib/axios";
import { 
    PlaceOrderPayload, 
    PlaceOrderResponse, 
    OrderListResponse,
    OrderDetailsResponse,
    CancelReasonsResponse,
    CancelOrderPayload,
    CancelOrderResponse
} from "@/types/order";

export const placeOrder = async (payload: PlaceOrderPayload): Promise<PlaceOrderResponse> => {
    const { data } = await categoryApi.post<PlaceOrderResponse>("/member/orders", payload);
    return data;
};

export const getOrders = async (userId: number, page: number = 1): Promise<OrderListResponse> => {
    const { data } = await categoryApi.get<OrderListResponse>(
        `/member/orders?user_id=${userId}&page=${page}`
    );
    return data;
};

export const getOrderDetails = async (orderId: string): Promise<OrderDetailsResponse> => {
    const { data } = await categoryApi.get<OrderDetailsResponse>(`/member/orders/${orderId}`);
    return data;
};

export const getCancelReasons = async (): Promise<CancelReasonsResponse> => {
    const { data } = await categoryApi.get<CancelReasonsResponse>("/member/cancel-reasons");
    return data;
};

export const cancelOrder = async (payload: CancelOrderPayload): Promise<CancelOrderResponse> => {
    const formData = new FormData();
    formData.append("order_id", payload.order_id);
    formData.append("cancel_reason_id", payload.cancel_reason_id);
    if (payload.cancel_note) {
        formData.append("cancel_note", payload.cancel_note);
    }

    const { data } = await categoryApi.post<CancelOrderResponse>(
        "/member/orders/cancel-orders", 
        formData
    );
    return data;
};
