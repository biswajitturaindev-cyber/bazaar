export interface UserAddress {
    id: string | number;
    user_id?: number;
    type?: string; // e.g. "home", "work", "other"
    full_name?: string;
    name?: string; // from test.resheragroup.in api
    phone?: string;
    email?: string | null;
    address?: string; // from test.resheragroup.in api
    address_line_1?: string;
    address_line_2?: string | null;
    landmark?: string | null;
    city?: any; // string or { id, name }
    state?: any; // string or { id, name }
    country?: string | null;
    postal_code?: string;
    latitude?: string | number | null;
    longitude?: string | number | null;
    is_default?: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface UserAddressListResponse {
    status: boolean;
    message?: string;
    data: {
        billing?: any;
        shipping?: UserAddress[];
        [key: string]: any;
    } | UserAddress[];
}
