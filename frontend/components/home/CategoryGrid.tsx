import CategoryCard from "./CategoryCard";
import { useBusinessCategories } from "@/hooks/category/useBusinessCategories";

export default function CategoryGrid() {
    const { data: apiResponse, isLoading, isError } = useBusinessCategories();

    if (isLoading) {
        return (
            <section>
                <h2 className="mb-4 text-lg font-semibold">
                    Shop by Category
                </h2>
                <div className="grid grid-cols-2 gap-4">
                    {[1, 2, 3, 4].map((n) => (
                        <div key={n} className="h-36 sm:h-40 bg-zinc-200 dark:bg-zinc-800 rounded-2xl animate-pulse" />
                    ))}
                </div>
            </section>
        );
    }

    if (isError) {
        return (
            <section>
                <h2 className="mb-4 text-lg font-semibold">
                    Shop by Category
                </h2>
                <div className="rounded-2xl border border-red-500/20 bg-red-500/10 p-4 text-center text-sm text-red-500">
                    Failed to load categories. Please try again later.
                </div>
            </section>
        );
    }

    const categoriesData = apiResponse?.data || [];

    if (categoriesData.length === 0) {
        return (
            <section>
                <h2 className="mb-4 text-lg font-semibold">
                    Shop by Category
                </h2>
                <div className="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-8 text-center text-zinc-500 dark:text-zinc-400">
                    No categories available.
                </div>
            </section>
        );
    }

    return (
        <section>
            <h2 className="mb-4 text-lg font-semibold">
                Shop by Category
            </h2>

            <div className="grid grid-cols-2 gap-4">
                {categoriesData.map((item) => (
                    <CategoryCard
                        key={item.id}
                        category={{
                            id: item.id,
                            title: item.name,
                            image: item.image || "https://images.unsplash.com/photo-1610348725531-843dff563e2c",
                            href: `/category/${item.id}`,
                        }}
                    />
                ))}
            </div>
        </section>
    );
}
