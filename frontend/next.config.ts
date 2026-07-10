import type { NextConfig } from "next";

const nextConfig: NextConfig = {
    images: {
        remotePatterns: [
            {
                protocol: "http",
                hostname: "bazaar.resheragroup.in",
            },
        ],
    },
};

export default nextConfig;
