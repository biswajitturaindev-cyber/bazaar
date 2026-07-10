"use client";

import { useState } from "react";
import { useParams } from "next/navigation";
import { useBusinessSubCategories } from "@/hooks/category/useBusinessSubCategories";
import CategoryCard from "@/components/home/CategoryCard";
import SubcategoryHeader from "@/components/category/SubcategoryHeader";
import BottomNavigation from "@/components/home/BottomNavigation";
import { useAuthContext } from "@/providers/AuthProvider";

export default function SubcategoryPage() {
    const params = useParams();
    const id = params.id as string;
    const [search, setSearch] = useState("");
    const { openLocationPicker } = useAuthContext();

    const { data: apiResponse, isLoading, isError } = useBusinessSubCategories(id, search);

    const subcategories = apiResponse?.data || [];
    const categoryName = subcategories[0]?.category?.name || "Subcategories";

    return (
        <main className="mx-auto min-h-screen max-w-3xl bg-gray-100 pb-24">
            <SubcategoryHeader
                title={categoryName}
                onOpenLocation={openLocationPicker}
                search={search}
                onSearchChange={setSearch}
            />

            <div className="p-4">
                {isLoading ? (
                    <div className="grid grid-cols-2 gap-4">
                        {[1, 2, 3, 4, 5, 6].map((n) => (
                            <div key={n} className="h-36 sm:h-40 bg-zinc-200 dark:bg-zinc-800 rounded-2xl animate-pulse" />
                        ))}
                    </div>
                ) : isError ? (
                    <div className="rounded-2xl border border-red-500/20 bg-red-500/10 p-4 text-center text-sm text-red-500">
                        Failed to load subcategories. Please try again later.
                    </div>
                ) : subcategories.length === 0 ? (
                    <div className="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-8 text-center text-zinc-500 dark:text-zinc-400">
                        No subcategories found.
                    </div>
                ) : (
                    <div className="grid grid-cols-2 gap-4">
                        {subcategories.map((sub) => (
                            <CategoryCard
                                key={sub.id}
                                category={{
                                    id: sub.id,
                                    title: sub.name,
                                    image: sub.image || "https://images.unsplash.com/photo-1504674900247-0877df9cc836",
                                    href: `/category/${id}/subcategory/${sub.id}`,
                                }}
                            />
                        ))}
                    </div>
                )}
            </div>

            <BottomNavigation />
        </main>
    );
}
