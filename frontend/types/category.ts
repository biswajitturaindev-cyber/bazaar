export interface BusinessCategory {
    id: string;
    name: string;
    image: string | null;
    status: number;
    status_label: string;
    created_at: string;
}

export interface BusinessCategoryResponse {
    data: BusinessCategory[];
}

export interface BusinessSubCategory {
    id: string;
    name: string;
    image: string | null;
    status: number;
    commission: string;
    status_label: string;
    category: {
        id: string;
        name: string;
    };
    created_at: string;
}

export interface BusinessSubCategoryResponse {
    data: BusinessSubCategory[];
}
