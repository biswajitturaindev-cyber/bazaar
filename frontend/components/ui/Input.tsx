import React from "react";

interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
    label?: string;
    error?: string;
}

export default function Input({
                                  label,
                                  error,
                                  className = "",
                                  ...props
                              }: InputProps) {
    return (
        <div className="space-y-2">
            {label && (
                <label className="text-sm font-medium text-white">
                    {label}
                </label>
            )}

            <input
                {...props}
                className={`
          w-full rounded-xl border border-gray-500
          bg-[rgb(34,34,43)]/50
          px-5 py-3
          text-white
          placeholder:text-gray-500
          outline-none
          transition
          focus:border-[#FF6600]
          focus:ring-2
          focus:ring-[#FF6600]/30
          ${className}
        `}
            />

            {error && (
                <p className="text-sm text-red-500">
                    {error}
                </p>
            )}
        </div>
    );
}
