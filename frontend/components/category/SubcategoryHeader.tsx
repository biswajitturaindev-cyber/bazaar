"use client";

import { RiArrowDownSLine, RiArrowLeftLine } from "react-icons/ri";
import SearchBar from "@/components/home/SearchBar";
import Link from "next/link";

interface SubcategoryHeaderProps {
    title: string;
    onOpenLocation: () => void;
    search: string;
    onSearchChange: (value: string) => void;
}

export default function SubcategoryHeader({
    title,
    onOpenLocation,
    search,
    onSearchChange,
}: SubcategoryHeaderProps) {
    return (
        <header className="bg-gradient-to-b from-purple-600 to-purple-800 p-4 pb-6">
            <div className="flex items-center gap-3 text-white">
                <Link href="/home" className="hover:opacity-85 flex items-center justify-center p-1 bg-white/10 rounded-full">
                    <RiArrowLeftLine size={20} />
                </Link>

                <div className="flex-1">
                    <h1 className="text-base font-bold truncate">
                        {title}
                    </h1>

                    <button
                        onClick={onOpenLocation}
                        className="text-[13px] text-white/80 flex items-center gap-1"
                    >
                        Katju Nagar, Jadavpur
                        <RiArrowDownSLine size={18} />
                    </button>
                </div>
            </div>

            <SearchBar value={search} onChange={onSearchChange} />
        </header>
    );
}
