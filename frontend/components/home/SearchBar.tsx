"use client";

import { RiSearchLine } from "react-icons/ri";

interface SearchBarProps {
    value: string;
    onChange: (value: string) => void;
}

export default function SearchBar({
                                      value,
                                      onChange,
                                  }: SearchBarProps) {
    return (
        <div className="mt-6 bg-white rounded-full flex items-center px-4 py-2 shadow">
            <RiSearchLine className="text-gray-500 text-lg" />

            <input
                type="text"
                value={value}
                onChange={(e) => onChange(e.target.value)}
                placeholder='Search "desired category"'
                className="flex-1 px-2 outline-none text-sm bg-transparent"
            />
        </div>
    );
}
