export interface ProductAttribute {
    attribute_id: string;
    attribute_name: string;
    value_id: string;
    value: string;
    color_code: string;
}

export interface ProductImage {
    id: string;
    image_large: string | null;
    image_medium: string | null;
    image_small: string | null;
}

export interface ProductVariant {
    variant_id: string;
    sku: string;
    barcode: string;
    mrp: number;
    cost_price: number;
    selling_price: number;
    discount: number;
    final_price: number;
    short_description: string | null;
    long_description: string | null;
    variant_status: string | null;
    manufacture_date: string | null;
    expiry_date: string | null;
    is_primary: boolean;
    total_stock: number;
    attributes: ProductAttribute[];
    images: ProductImage[];
    meta: Record<string, unknown> | null;
}

export interface Product {
    product_id: string;
    name: string;
    business: {
        business_id: string;
        business_name: string;
    };
    business_category_id: string;
    business_sub_category_id: string;
    category_id: string;
    sub_category_id: string;
    sub_sub_category_id: string;
    primary_variant: ProductVariant;
    mrp: number;
    cost_price: number;
    selling_price: number;
    discount: number;
    final_price: number;
    image: string;
    status: number;
    status_label: string;
    product_type: number;
    created_at: string;
}

export interface ProductResponse {
    status: boolean;
    message: string;
    data: Product[];
    meta: {
        current_page: number;
        per_page: number;
        total: number;
        last_page: number;
    };
}

export interface ProductDetails {
    product_id: string;
    name: string;
    business: {
        business_id: string;
        business_name: string;
    };
    business_category_id: string;
    business_sub_category_id: string;
    category_id: string;
    sub_category_id: string;
    sub_sub_category_id: string;
    variants: ProductVariant[];
    mrp: number;
    cost_price: number;
    selling_price: number;
    discount: number;
    final_price: number;
    image: string;
    status: number;
    status_label: string;
    product_type: number;
    created_at: string;
}

export interface ProductDetailsResponse {
    status: boolean;
    message: string;
    data: ProductDetails;
}
