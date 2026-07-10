import React from "react";

interface ButtonProps
    extends React.ButtonHTMLAttributes<HTMLButtonElement> {
    loading?: boolean;
}

export default function Button({
                                   children,
                                   loading,
                                   className = "",
                                   disabled,
                                   ...props
                               }: ButtonProps) {
    return (
        <button
            {...props}
            disabled={disabled || loading}
            className={`
        flex w-full items-center justify-center gap-2
        rounded-xl
        bg-gradient-to-r
        from-[#FF6600]
        to-orange-500
        px-5 py-3
        font-semibold
        text-white
        transition
        hover:opacity-90
        disabled:cursor-not-allowed
        disabled:opacity-60
        ${className}
      `}
        >
            {loading ? "Loading..." : children}
        </button>
    );
}
