export interface CartItemAttribute {
    attribute_master_id: string;
    attribute_value_id: string;
    attribute_name: string;
    attribute_value: string;
    color_code: string | null;
}

export interface CartItem {
    id: string;
    user: number;
    business: {
        id: string | null;
        name: string | null;
        gst_no: string | null;
        gst_state_code: string | null;
        gst_address: string | null;
    };
    business_category_id: string;
    product_id: string;
    product_variant_id: string | null;
    product_name: string;
    quantity: number;
    image: string | null;
    product: {
        name: string | null;
        final_price: number;
        image: string | null;
    };
    attributes: CartItemAttribute[];
}

export interface CartResponse {
    success: boolean;
    message: string;
    total_items: number;
    vendor_gst_details: {
        gst_no: string | null;
        gst_state_code: string | null;
        gst_address: string | null;
    } | null;
    data: CartItem[];
}

export interface AddToCartPayload {
    user_id: number;
    business_id: string;
    business_category_id: string;
    product_id: string;
    product_variant_id?: string | null;
    quantity: number;
    attributes?: Array<{
        attribute_master_id: string;
        attribute_value_id: string;
    }> | null;
}

export interface AddToCartResponse {
    success: boolean;
    message: string;
    action_required?: string; // e.g. "clear_cart"
    data?: CartItem;
}

export interface UpdateCartItemResponse {
    success: boolean;
    message: string;
    data: CartItem;
}

export interface RemoveCartItemResponse {
    success: boolean;
    message: string;
}

export interface ClearCartResponse {
    status: boolean;
    message: string;
}
