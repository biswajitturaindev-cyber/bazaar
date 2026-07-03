import { useMutation } from "@tanstack/react-query";
import { login } from "@/services/auth.service";
import { useRouter } from "next/navigation";
import { useState } from "react";
import { AxiosError } from "axios";
import { useAuthContext } from "@/providers/AuthProvider";

export const useLogin = () => {
    const router = useRouter();
    const { login: contextLogin } = useAuthContext();
    const [errorMsg, setErrorMsg] = useState<string | null>(null);

    const mutation = useMutation({
        mutationFn: login,
        onSuccess: (data) => {
            if (data?.status === false) {
                setErrorMsg(data.message || "Login failed");
                return;
            }

            const token = data?.token || data?.data?.token || data?.data?.access_token;
            const user = data?.user || data?.data?.user;

            if (token && user) {
                contextLogin(token, user);
                setErrorMsg(null);
                router.push("/home");
            } else {
                setErrorMsg("Login failed: Invalid session payload returned by server");
            }
        },
        onError: (err: AxiosError<{ message?: string }>) => {
            const message = err.response?.data?.message || err.message || "Something went wrong";
            setErrorMsg(message);
        },
    });

    return {
        ...mutation,
        errorMsg,
        setErrorMsg,
    };
};
