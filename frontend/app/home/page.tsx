"use client";

import Header from "@/components/home/Header";
import CategoryGrid from "@/components/home/CategoryGrid";
import BottomNavigation from "@/components/home/BottomNavigation";
import { useAuthContext } from "@/providers/AuthProvider";

export default function HomePage() {
    const { openLocationPicker } = useAuthContext();

    return (
        <main className="mx-auto min-h-screen max-w-3xl bg-gray-100 pb-24">
            <Header
                onOpenLocation={openLocationPicker}
            />

            <div className="p-4">
                <CategoryGrid />
            </div>

            <BottomNavigation />
        </main>
    );
}
