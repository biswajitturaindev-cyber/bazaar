"use client";

import { ProductAttribute } from "@/types/product";

interface SpecsTableProps {
    attributes: ProductAttribute[];
    sku?: string | null;
    barcode?: string | null;
    totalStock?: number | null;
    statusLabel?: string | null;
}

export default function SpecsTable({
    attributes,
    sku,
    barcode,
    totalStock,
    statusLabel = "Active",
}: SpecsTableProps) {
    const specsToRender = attributes.length > 0
        ? attributes.map((attr) => ({
              label: attr.attribute_name,
              value: attr.value,
          }))
        : [
              { label: "SKU / Code", value: sku || "N/A" },
              { label: "Barcode", value: barcode || "N/A" },
              { label: "Availability", value: totalStock !== undefined && totalStock !== null ? `${totalStock} units` : "In Stock" },
              { label: "Status", value: statusLabel || "Active" },
          ];

    return (
        <div className="pt-5 border-b border-slate-100 pb-5">
            <h3 className="font-bold text-gray-900 mb-3">Product Details</h3>
            <div className="overflow-hidden rounded-2xl border border-gray-200 shadow-sm bg-white">
                <table className="w-full text-sm text-left text-gray-700">
                    <tbody>
                        {specsToRender.map((spec, index) => (
                            <tr
                                key={index}
                                className={index < specsToRender.length - 1 ? "border-b border-gray-200" : ""}
                            >
                                <td className="w-[38%] bg-gray-50 px-4 py-3 font-semibold text-gray-800 border-r border-gray-200/50">
                                    {spec.label}
                                </td>
                                <td className="px-4 py-3 text-gray-600 font-medium">{spec.value}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
