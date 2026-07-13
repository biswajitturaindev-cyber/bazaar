"use client";

import React, { useState, useEffect, useRef } from "react";
import { RiArrowDownSLine, RiNotificationLine, RiUserLine, RiLogoutBoxRLine } from "react-icons/ri";
import { useAuthContext } from "@/providers/AuthProvider";
import { formatAddress } from "@/utils/address";

interface HeaderProps {
    onOpenLocation: () => void;
}

export default function Header({ onOpenLocation }: HeaderProps) {
    const { user, logout, selectedAddress } = useAuthContext();
    const [isOpen, setIsOpen] = useState(false);
    const dropdownRef = useRef<HTMLDivElement>(null);

    // Close the dropdown when clicking outside
    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
                setIsOpen(false);
            }
        };

        if (isOpen) {
            document.addEventListener("mousedown", handleClickOutside);
        }
        return () => {
            document.removeEventListener("mousedown", handleClickOutside);
        };
    }, [isOpen]);

    return (
        <header className="sticky top-0 z-50 bg-gradient-to-b from-purple-600 to-purple-800 p-4 pb-6">
            <div className="flex justify-between items-center text-white">
                <div>
                    <h1 className="text-base font-bold transition-all duration-200">
                        {user?.name || "Guest User"}
                    </h1>

                    <button
                        onClick={onOpenLocation}
                        className="text-[13px] text-white/85 flex items-center gap-1 hover:text-white transition active:scale-[0.98] max-w-[220px] md:max-w-xs"
                    >
                        <span className="truncate">
                            {selectedAddress 
                                ? formatAddress(selectedAddress) 
                                : "Select delivery location"}
                        </span>
                        <RiArrowDownSLine size={18} className="shrink-0" />
                    </button>
                </div>

                <div className="flex gap-3 items-center">
                    <button className="relative bg-white/20 hover:bg-white/30 rounded-full p-3 transition active:scale-95">
                        <RiNotificationLine size={20} />
                        <span className="absolute top-2 right-2 h-2 w-2 rounded-full bg-red-500 animate-pulse" />
                    </button>

                    <div className="relative">
                        <button
                            onClick={() => setIsOpen(!isOpen)}
                            className={`rounded-full p-3 transition active:scale-95 ${
                                isOpen ? "bg-white/35 shadow-inner" : "bg-white/20 hover:bg-white/30"
                            }`}
                            aria-expanded={isOpen}
                            aria-haspopup="true"
                        >
                            <RiUserLine size={20} />
                        </button>

                        {/* Profile Dropdown */}
                        {isOpen && (
                            <div
                                ref={dropdownRef}
                                className="absolute right-0 mt-2.5 w-52 bg-white text-gray-800 rounded-2xl shadow-2xl border border-gray-100 p-1.5 z-50 animate-fade-in origin-top-right transform scale-100 opacity-100 transition-all"
                            >
                                <div className="px-3.5 py-2.5 border-b border-gray-100">
                                    <p className="text-xs font-bold text-gray-800 truncate">
                                        {user?.name || "Guest User"}
                                    </p>
                                </div>

                                <button
                                    onClick={() => {
                                        setIsOpen(false);
                                        logout();
                                    }}
                                    className="w-full text-left px-3.5 py-2.5 text-xs font-bold text-red-600 hover:bg-red-50 rounded-xl transition duration-150 flex items-center gap-2 mt-1"
                                >
                                    <RiLogoutBoxRLine size={15} />
                                    Logout
                                </button>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </header>
    );
}
