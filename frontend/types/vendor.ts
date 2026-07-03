export interface VendorKycImage {
    url: string | null;
    number: string | null;
    status: number;
    status_label: string;
    state_code?: string;
    gst_address?: string;
}

export interface VendorKycDetail {
    id: string;
    business_id: string;
    owner_photo: VendorKycImage;
    shop_photo: VendorKycImage;
    pan_card: VendorKycImage;
    gst_certificate: VendorKycImage;
    trade_license: VendorKycImage;
    fssai_license: VendorKycImage;
    address_proof: VendorKycImage;
    commission_distribution: boolean;
    commission_distribution_label: string;
    created_at: string;
}

export interface VendorUser {
    id: string;
    vendor_id: string;
    name: string;
    email: string;
    mobile: string;
    dob: string | null;
    gender: number;
    status: number;
    wallet1: string;
    wallet2: string;
    wallet3: string;
    kyc_status: string;
    profile_status: string;
}

export interface Vendor {
    id: string;
    user_id: string;
    sponsor_id: number;
    business_type: string | null;
    business_name: string;
    business_category_id: string;
    business_sub_category_id: string;
    years_in_business: number | null;
    gst_number: string;
    pan_number: string;
    fssai_license: string | null;
    registration_number: string | null;
    created_at: string;
    updated_at: string;
    category: {
        id: string;
        name: string;
    };
    sub_category: {
        id: string;
        name: string;
    };
    user: VendorUser;
    kycdetail: VendorKycDetail;
}

export interface VendorResponse {
    data: Vendor[];
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
