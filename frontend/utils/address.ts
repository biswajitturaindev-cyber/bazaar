import { UserAddress } from "@/types/address";

export function formatAddress(addr: UserAddress | null | undefined): string {
    if (!addr) return "";

    const street = addr.address_line_1 || addr.address || "";

    const suite = addr.address_line_2 || "";

    const landmark = addr.landmark || "";

    let city = "";
    if (addr.city) {
        if (typeof addr.city === "object") {
            city = addr.city.name || addr.city.city_name || "";
        } else {
            city = addr.city;
        }
    }

    let state = "";
    if (addr.state) {
        if (typeof addr.state === "object") {
            state = addr.state.name || addr.state.state_name || "";
        } else {
            state = addr.state;
        }
    }

    const pin = addr.postal_code || (addr as any).pincode || (addr as any).pin || "";

    // Build the formatted parts
    const parts: string[] = [];
    if (street) parts.push(street);
    if (suite) parts.push(suite);
    if (landmark) parts.push(landmark);
    if (city) parts.push(city);
    if (state) parts.push(state);
    if (pin) parts.push(pin);

    return parts.filter(Boolean).join(", ");
}
