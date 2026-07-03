"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";

export default function RootPage() {
    const router = useRouter();

    useEffect(() => {
        router.replace("/home");
    }, [router]);

    return (
        <div className="flex items-center justify-center min-h-screen bg-slate-50">
            <div className="flex flex-col items-center gap-3">
                <div className="w-8 h-8 border-4 border-purple-600 border-t-transparent rounded-full animate-spin" />
                <p className="text-xs font-bold text-gray-400 tracking-wider">Loading Bazaar...</p>
            </div>
        </div>
    );
}
