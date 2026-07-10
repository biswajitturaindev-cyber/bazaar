"use client";

interface LongDescriptionProps {
    htmlContent?: string | null;
    shortDescription?: string | null;
}

export default function LongDescription({
                                            htmlContent,
                                            shortDescription,
                                        }: LongDescriptionProps) {
    const hasHtml = !!htmlContent?.trim();
    const hasShort = !!shortDescription?.trim();

    return (
        <div className="pt-5 pb-8">
            <h3 className="mb-4 font-bold text-gray-900">
                Description
            </h3>

            {/* Short Description */}
            {hasShort && (
                <div className="mb-6">
                    <h4 className="mb-2 text-sm font-semibold text-gray-900">
                        Short Description
                    </h4>

                    <p className="text-sm leading-7 text-gray-600">
                        {shortDescription}
                    </p>
                </div>
            )}

            {/* Long Description */}
            {hasHtml && (
                <div>
                    <h4 className="mb-2 text-sm font-semibold text-gray-900">
                        Long Description
                    </h4>

                    <div
                        className="text-sm leading-7 text-gray-600"
                        dangerouslySetInnerHTML={{
                            __html: htmlContent!,
                        }}
                    />
                </div>
            )}

            {!hasShort && !hasHtml && (
                <p className="text-sm italic leading-7 text-gray-500">
                    No description available for this product.
                </p>
            )}
        </div>
    );
}
