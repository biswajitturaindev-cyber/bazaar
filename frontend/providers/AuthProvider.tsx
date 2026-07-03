"use client";

import React, { createContext, useContext, useEffect, useState } from "react";
import { useRouter, usePathname } from "next/navigation";
import { decodeId } from "@/utils/hashids";
import { UserAddress } from "@/types/address";
import LocationSheet from "@/components/home/LocationSheet";

export interface User {
    id?: string; // hashed string
    user_id?: number; // raw integer
    username?: string;
    vendor_id?: string;
    name: string;
    email: string;
    mobile?: string;
    phone?: number | string;
    profile_image?: string;
    [key: string]: any;
}

export interface AuthContextType {
    token: string | null;
    user: User | null;
    userId: number | null;
    isAuthenticated: boolean;
    isLoading: boolean;
    selectedAddress: UserAddress | null;
    selectAddress: (address: UserAddress) => void;
    openLocationPicker: () => void;
    login: (token: string, user: User) => void;
    logout: () => void;
}

const AuthContext = createContext<AuthContextType | null>(null);

export default function AuthProvider({ children }: { children: React.ReactNode }) {
    const router = useRouter();
    const pathname = usePathname();

    const [token, setToken] = useState<string | null>(null);
    const [user, setUser] = useState<User | null>(null);
    const [userId, setUserId] = useState<number | null>(null);
    const [selectedAddress, setSelectedAddress] = useState<UserAddress | null>(null);
    const [isLocationPickerOpen, setIsLocationPickerOpen] = useState(false);
    const [isLoading, setIsLoading] = useState(true);

    // Initialize state from localStorage on startup
    useEffect(() => {
        if (typeof window !== "undefined") {
            const savedToken = localStorage.getItem("token");
            const savedUser = localStorage.getItem("user");
            const savedAddress = localStorage.getItem("selected_address");

            if (savedToken && savedUser) {
                setToken(savedToken);
                try {
                    const parsedUser = JSON.parse(savedUser) as User;
                    setUser(parsedUser);
                    if (parsedUser?.user_id) {
                        setUserId(parsedUser.user_id);
                    } else if (parsedUser?.id) {
                        const numericId = decodeId(parsedUser.id);
                        setUserId(numericId);
                    }
                } catch (e) {
                    console.error("Failed to parse user from localStorage", e);
                }
            }

            if (savedAddress) {
                try {
                    setSelectedAddress(JSON.parse(savedAddress) as UserAddress);
                } catch (e) {
                    console.error("Failed to parse selected address from localStorage", e);
                }
            }
            setIsLoading(false);
        }
    }, []);

    const login = (newToken: string, newUser: User) => {
        if (typeof window !== "undefined") {
            localStorage.setItem("token", newToken);
            localStorage.setItem("user", JSON.stringify(newUser));
            document.cookie = `token=${newToken}; path=/; max-age=86400; SameSite=Lax`;
            
            setToken(newToken);
            setUser(newUser);
            if (newUser?.user_id) {
                setUserId(newUser.user_id);
            } else if (newUser?.id) {
                setUserId(decodeId(newUser.id));
            }
        }
    };

    const logout = () => {
        if (typeof window !== "undefined") {
            localStorage.removeItem("token");
            localStorage.removeItem("user");
            localStorage.removeItem("selected_address");
            document.cookie = "token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT";

            setToken(null);
            setUser(null);
            setUserId(null);
            setSelectedAddress(null);
            setIsLocationPickerOpen(false);
            router.push("/login");
        }
    };

    const selectAddress = (addr: UserAddress) => {
        if (typeof window !== "undefined") {
            localStorage.setItem("selected_address", JSON.stringify(addr));
            setSelectedAddress(addr);
            setIsLocationPickerOpen(false);
        }
    };

    const openLocationPicker = () => {
        setIsLocationPickerOpen(true);
    };

    const isAuthenticated = !!token && userId !== null;
    const isPublicRoute = pathname === "/login";

    // Route Protection & Redirect logic
    useEffect(() => {
        // ALWAYS wait for initialization to complete before checking credentials and redirecting!
        if (!isLoading) {
            if (!isAuthenticated && !isPublicRoute) {
                router.replace("/login");
            } else if (isAuthenticated && isPublicRoute) {
                router.replace("/home");
            }
        }
    }, [isAuthenticated, isLoading, isPublicRoute, router]);

    const value: AuthContextType = {
        token,
        user,
        userId,
        isAuthenticated,
        isLoading,
        selectedAddress,
        selectAddress,
        openLocationPicker,
        login,
        logout,
    };

    // Prevent screen flashes / loops during checking auth startup
    if (isLoading) {
        return (
            <div className="flex items-center justify-center min-h-screen bg-slate-50">
                <div className="flex flex-col items-center gap-3">
                    <div className="w-8 h-8 border-4 border-purple-600 border-t-transparent rounded-full animate-spin" />
                    <p className="text-xs font-bold text-gray-400 uppercase tracking-wider">Loading Bazaar...</p>
                </div>
            </div>
        );
    }

    // Guard private components from showing raw contents while redirecting
    if (!isAuthenticated && !isPublicRoute) {
        return (
            <div className="flex items-center justify-center min-h-screen bg-slate-50">
                <div className="flex flex-col items-center gap-3">
                    <div className="w-8 h-8 border-4 border-purple-600 border-t-transparent rounded-full animate-spin" />
                    <p className="text-xs font-bold text-gray-400 uppercase tracking-wider">Redirecting...</p>
                </div>
            </div>
        );
    }

    // Modal is forced open on login if the user has no selected delivery address
    const showLocationPicker = isAuthenticated && (!selectedAddress || isLocationPickerOpen);
    const preventClosePicker = isAuthenticated && !selectedAddress;

    return (
        <AuthContext.Provider value={value}>
            {children}
            <LocationSheet
                open={showLocationPicker}
                onClose={() => setIsLocationPickerOpen(false)}
                onSelectAddress={selectAddress}
                selectedAddressId={selectedAddress?.id}
                preventClose={preventClosePicker}
            />
        </AuthContext.Provider>
    );
}

export const useAuthContext = () => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error("useAuthContext must be used within an AuthProvider");
    }
    return context;
};
