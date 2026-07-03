"use client";

import Image from "next/image";
import { useState } from "react";
import { RiEyeLine, RiEyeOffLine, RiArrowRightLine } from "react-icons/ri";
import { Images } from "@/assets/images";
import { useLogin } from "@/hooks/auth/useLogin";

export default function LoginPage() {
    const [showPassword, setShowPassword] = useState(false);
    const { mutate, isPending, errorMsg } = useLogin();

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        const formData = new FormData(e.currentTarget);
        const username = formData.get("username") as string;
        const password = formData.get("password") as string;
        if (username && password) {
            mutate({ username, password });
        }
    };

    return (
        <section className="flex items-center justify-center min-h-screen  bg-gray-600">
            <div className="bg-[#1c1c20]/60 backdrop-blur-xl rounded-3xl px-4 sm:px-6 py-8 border border-white/20 w-full md:w-96 mx-6 shadow-[0_8px_32px_rgba(0,0,0,0.4)]">
                {/* Logo */}
                <div className="flex justify-center mb-6">
                    <div className="relative h-28 w-28 flex items-center justify-center">
                        <div className="absolute inset-0 rounded-full bg-gradient-to-tr from-orange-600 to-orange-50 animate-spin-slow" />

                        <div className="relative bg-white rounded-full p-3 h-24 w-24 flex items-center justify-center">
                            <Image
                                src={Images.logo}
                                alt="Logo"
                                width={80}
                                height={80}
                                className="object-contain"
                            />
                        </div>
                    </div>
                </div>

                {/* Heading */}
                <div className="text-center mb-4">
                    <h2 className="text-white text-xl font-semibold">
                        Login to Your Account
                    </h2>

                    <p className="text-gray-400 text-sm mt-1">
                        Enter your credentials to continue
                    </p>
                </div>

                {/* Form */}
                <form onSubmit={handleSubmit} className="space-y-5">
                    {/* Error Message */}
                    {errorMsg && (
                        <div className="bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl px-4 py-3 text-sm text-center">
                            {errorMsg}
                        </div>
                    )}

                    {/* Username */}
                    <input
                        type="text"
                        name="username"
                        required
                        placeholder="Enter username"
                        className="w-full mt-2 bg-[rgb(34,34,43)]/50 border border-gray-500 focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/30 outline-none rounded-xl px-5 py-3 text-white placeholder-gray-500 transition"
                    />

                    {/* Password */}
                    <div className="relative">
                        <input
                            type={showPassword ? "text" : "password"}
                            name="password"
                            required
                            placeholder="••••••••••"
                            className="w-full bg-[rgb(34,34,43)]/50 border border-gray-500 focus:border-[#FF6600] focus:ring-2 focus:ring-[#FF6600]/30 outline-none rounded-xl px-5 py-3 text-white placeholder-gray-500 transition"
                        />

                        <button
                            type="button"
                            onClick={() => setShowPassword(!showPassword)}
                            className="absolute right-4 top-1/2 -translate-y-1/2"
                        >
                            {showPassword ? (
                                <RiEyeLine className="text-orange-500 text-xl" />
                            ) : (
                                <RiEyeOffLine className="text-gray-500 text-xl" />
                            )}
                        </button>
                    </div>

                    {/* Login Button */}
                    <button
                        type="submit"
                        disabled={isPending}
                        className="w-full mt-4 bg-gradient-to-r from-[#FF6600] to-orange-500 hover:opacity-90 disabled:opacity-50 text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2"
                    >
                        {isPending ? "Logging in..." : "Login"}
                        {!isPending && <RiArrowRightLine size={20} />}
                    </button>
                </form>
            </div>
        </section>
    );
}
