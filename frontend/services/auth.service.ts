import api from "@/lib/axios";

export interface LoginPayload {
    username: string;
    password: string;
}

export const login = async (payload: LoginPayload) => {
    const formData = new FormData();

    formData.append("username", payload.username);
    formData.append("password", payload.password);

    const { data } = await api.post("/member/v1/login", formData);

    return data;
};

