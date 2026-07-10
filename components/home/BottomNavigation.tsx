"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import {
    RiHome5Fill,
    RiLayoutGridLine,
} from "react-icons/ri";
import { FiShoppingBag } from "react-icons/fi";

export default function BottomNavigation() {
    const pathname = usePathname();

    const menus = [
        {
            name: "Home",
            href: "/home",
            icon: RiHome5Fill,
        },
        {
            name: "Category",
            href: "/category",
            icon: RiLayoutGridLine,
        },
        {
            name: "Orders",
            href: "/orders",
            icon: FiShoppingBag,
        },
    ];

    return (
        <footer className="fixed bottom-0 left-1/2 z-50 w-full max-w-3xl -translate-x-1/2 border-t border-slate-100 bg-white px-2 py-3">
            <div className="flex items-center justify-between">
                {menus.map((menu) => {
                    const Icon = menu.icon;

                    const active = pathname === menu.href;

                    return (
                        <Link
                            key={menu.name}
                            href={menu.href}
                            className={`flex flex-1 flex-col items-center text-xs ${
                                active
                                    ? "text-purple-600"
                                    : "text-gray-500"
                            }`}
                        >
                            <Icon size={22} />
                            {menu.name}
                        </Link>
                    );
                })}

                <Link
                    href="/dashboard"
                    className="rounded-full bg-gradient-to-b from-purple-500 to-purple-700 px-5 py-3 text-xs font-semibold text-white"
                >
                    Reshera
                </Link>
            </div>
        </footer>
    );
}
