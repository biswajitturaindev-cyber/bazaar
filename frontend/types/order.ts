export interface PlaceOrderPayload {
    user_id: number;
    phone?: string;
    billing_address: string;
    billing_city_id?: number | null;
    billing_state_id?: number | null;
    billing_pincode?: string;
    shipping_address_id?: number | null;
    shipping_address: string;
    shipping_city_id?: number | null;
    shipping_state_id?: number | null;
    shipping_pincode?: string;
    platformCharge?: number;
    deliveryCharge?: number;
    taxAmount?: number;
    discountAmount?: number;
    itemsTotal?: number;
    grandTotal?: number;
    payment_method?: "WALLET" | "ONLINE" | "COD";
    loyalty_points?: number;
    notes?: string;
    is_gst_bill?: boolean;
    gst_name?: string;
    gst_number?: string;
    gst_address?: string;
}

export interface PlaceOrderResponse {
    success: boolean;
    message: string;
    data?: {
        id: string;
        order_no: string;
        invoice_no: string;
        grand_total: number;
        [key: string]: unknown;
    };
}

export interface OrderItemAttribute {
    attribute_id: number;
    attribute_value_id: number;
    name: string;
    value: string;
    color_code: string | null;
}

export interface OrderItem {
    id: string;
    product_id: number;
    product_variant_id: number | null;
    product_name: string;
    sku: string | null;
    commission: number;
    vendor_commission: number;
    commission_percent: number;
    commission_amount: number;
    image: string | null;
    quantity: number;
    modified_quantity: number | null;
    status: string | null;
    cancel_reason_id: string | null;
    cancel_note: string | null;
    cancelled_by: string | null;
    cancelled_at: string | null;
    mrp: number;
    selling_price: number;
    discount_amount: number;
    final_price: number;
    subtotal: number;
    loyalty_points: number;
    attributes: OrderItemAttribute[];
    product_snapshot: any;
}

export interface OrderAddress {
    id: number;
    order_id: number;
    phone: string | null;
    billing_address: string;
    shipping_address: string;
    [key: string]: any;
}

export interface OrderStatusHistory {
    id: number;
    order_id: number;
    status: number;
    remarks: string | null;
    created_at: string;
    [key: string]: any;
}

export interface OrderBusiness {
    id: number;
    business_name: string;
    [key: string]: any;
}

export interface Order {
    id: string;
    order_no: string;
    invoice_no: string;
    business_id: string;
    business_category_id: number;
    user_id: number;
    payment_status: number;
    payment_status_label: string;
    payment_method: number;
    payment_method_label: string;
    order_status: number;
    order_status_label: string;
    total_items: number;
    items_total: number;
    discount_amount: number;
    platform_charge: number;
    delivery_charge: number;
    tax_amount: number;
    grand_total: number;
    loyalty_used: number;
    loyalty_earned: number;
    wallet_used: number;
    online_paid: number;
    notes: string | null;
    cancel_reason_id?: string | null;
    cancel_note?: string | null;
    cancelled_at?: string | null;
    items?: OrderItem[];
    addresses?: OrderAddress[];
    status_histories?: OrderStatusHistory[];
    business?: OrderBusiness;
    placed_at: string;
    created_at: string;
    updated_at: string;
}

export interface OrderListResponse {
    data: Order[];
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
}

export interface OrderDetailsResponse {
    success: boolean;
    data: Order;
}

export interface CancelReason {
    id: string;
    reason: string;
    status: boolean;
}

export interface CancelReasonsResponse {
    success: boolean;
    message: string;
    data: CancelReason[];
}

export interface CancelOrderPayload {
    order_id: string;
    cancel_reason_id: string;
    cancel_note?: string;
}

export interface CancelOrderResponse {
    success: boolean;
    message: string;
}
