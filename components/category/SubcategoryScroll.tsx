"use client";

import { useRouter } from "next/navigation";
import { BusinessSubCategory } from "@/types/category";

interface SubcategoryScrollProps {
    categoryId: string;
    subCategoryId: string;
    subcategories: BusinessSubCategory[];
}

export default function SubcategoryScroll({
    categoryId,
    subCategoryId,
    subcategories,
}: SubcategoryScrollProps) {
    const router = useRouter();

    return (
        <section className="mt-2 px-4 overflow-x-auto no-scrollbar">
            <div className="flex gap-4 w-max py-2">
                {subcategories.map((sub) => {
                    const isActive = sub.id === subCategoryId;
                    const subImage = sub.image || "https://images.unsplash.com/photo-1511707171634-5f897ff02aa9";
                    return (
                        <button
                            key={sub.id}
                            onClick={() => router.push(`/category/${categoryId}/subcategory/${sub.id}`)}
                            className="flex flex-col items-center cursor-pointer group"
                        >
                            <span className={`w-14 h-14 rounded-full flex items-center justify-center transition ${
                                isActive ? "bg-purple-600 scale-105 shadow-md" : "bg-purple-100 group-hover:bg-purple-200"
                            }`}>
                                <img src={subImage} alt="" className="w-12 h-12 object-cover rounded-full" />
                            </span>
                            <span className={`text-[11px] mt-1 font-medium transition ${
                                isActive ? "text-purple-700 font-semibold" : "text-gray-600 group-hover:text-gray-900"
                            }`}>
                                {sub.name}
                            </span>
                        </button>
                    );
                })}
            </div>
        </section>
    );
}
