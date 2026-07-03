import Image from "next/image";
import Link from "next/link";

export interface Category {
    id: number | string;
    title: string;
    image: string;
    href: string;
}

interface Props {
    category: Category;
}

export default function CategoryCard({ category }: Props) {
    return (
        <Link
            href={category.href}
            className="group relative block h-36 sm:h-40 overflow-hidden rounded-2xl shadow-lg"
        >
            <Image
                src={category.image}
                alt={category.title}
                fill
                className="object-cover transition duration-500 group-hover:scale-110"
            />

            <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />

            <div className="absolute bottom-3 left-3 right-3 text-lg font-bold leading-snug text-white">
                {category.title}
            </div>
        </Link>
    );
}
