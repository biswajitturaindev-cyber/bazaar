import Hashids from "hashids";

const hashids = new Hashids("", 10, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890");

export const decodeId = (hashedId: string): number | null => {
    if (!hashedId) return null;
    try {
        const decoded = hashids.decode(hashedId);
        return decoded.length > 0 ? (decoded[0] as number) : null;
    } catch (e) {
        console.error("Failed to decode ID:", hashedId, e);
        return null;
    }
};

export const encodeId = (numericId: number): string => {
    return hashids.encode(numericId);
};
export default hashids;
