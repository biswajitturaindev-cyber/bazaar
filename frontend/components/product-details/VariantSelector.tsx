"use client";

import { ProductVariant } from "@/types/product";

interface VariantSelectorProps {
    variants: ProductVariant[];
    activeVariant: ProductVariant | null;
    onSelectVariant: (variantId: string) => void;
}

export default function VariantSelector({
    variants,
    activeVariant,
    onSelectVariant,
}: VariantSelectorProps) {
    if (!variants || variants.length <= 1 || !activeVariant) {
        return null;
    }

    // 1. Group all attributes across all variants to get unique options
    const attributesMap: Record<string, { value: string; colorCode: string }[]> = {};

    variants.forEach((v) => {
        v.attributes.forEach((attr) => {
            if (!attributesMap[attr.attribute_name]) {
                attributesMap[attr.attribute_name] = [];
            }
            const exists = attributesMap[attr.attribute_name].some((item) => item.value === attr.value);
            if (!exists) {
                attributesMap[attr.attribute_name].push({
                    value: attr.value,
                    colorCode: attr.color_code,
                });
            }
        });
    });

    // 2. Identify the active variant's current attribute choices
    const currentAttributes: Record<string, string> = {};
    activeVariant.attributes.forEach((attr) => {
        currentAttributes[attr.attribute_name] = attr.value;
    });

    // 3. Helper to switch variants when an attribute option is clicked
    const handleSelectOption = (attrName: string, value: string) => {
        const targetAttributes = { ...currentAttributes, [attrName]: value };

        let bestVariant = variants[0];
        let bestMatchCount = -1;

        variants.forEach((v) => {
            let matchCount = 0;
            v.attributes.forEach((attr) => {
                if (targetAttributes[attr.attribute_name] === attr.value) {
                    matchCount++;
                }
            });
            if (matchCount > bestMatchCount) {
                bestMatchCount = matchCount;
                bestVariant = v;
            }
        });

        if (bestVariant) {
            onSelectVariant(bestVariant.variant_id);
        }
    };

    return (
        <div className="space-y-5 pt-4 border-b border-slate-100 pb-5">
            {Object.entries(attributesMap).map(([attrName, options]) => {
                const isColor = attrName.toLowerCase() === "color" || attrName.toLowerCase() === "colors";

                return (
                    <div key={attrName} className="space-y-3">
                        <h3 className="font-semibold text-sm text-gray-900">Select {attrName}</h3>
                        <div className="flex flex-wrap gap-2.5">
                            {options.map((opt) => {
                                const isSelected = currentAttributes[attrName] === opt.value;

                                if (isColor) {
                                    // Render color circular selector
                                    const hasHexColor = opt.colorCode && opt.colorCode.startsWith("#");
                                    return (
                                        <button
                                            key={opt.value}
                                            onClick={() => handleSelectOption(attrName, opt.value)}
                                            style={{ backgroundColor: hasHexColor ? opt.colorCode : undefined }}
                                            className={`w-8 h-8 rounded-full border border-black/10 transition relative ${
                                                isSelected ? "ring-2 ring-offset-2 ring-purple-600 scale-[1.03]" : "hover:scale-105"
                                            } ${!hasHexColor ? "bg-purple-100 flex items-center justify-center text-[10px] font-semibold text-purple-700" : ""}`}
                                            title={opt.value}
                                        >
                                            {!hasHexColor && opt.value.substring(0, 2)}
                                        </button>
                                    );
                                }

                                // Render standard size/option pill button
                                return (
                                    <button
                                        key={opt.value}
                                        onClick={() => handleSelectOption(attrName, opt.value)}
                                        className={`px-4 py-2 rounded-full border text-xs font-semibold transition ${
                                            isSelected
                                                ? "bg-purple-600 border-purple-600 text-white shadow-sm"
                                                : "border-gray-300 text-gray-800 hover:border-gray-400 bg-white"
                                        }`}
                                    >
                                        {opt.value}
                                    </button>
                                );
                            })}
                        </div>
                    </div>
                );
            })}
        </div>
    );
}
