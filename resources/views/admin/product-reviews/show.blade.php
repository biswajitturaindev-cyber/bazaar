@extends('admin.layouts.master')

@section('title')
    Product Details
@endsection

@section('breadcrumb')
    Product Details
@endsection

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=DM+Mono:wght@400;500&display=swap');
    @import url('https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.css');

    :root {
        --accent: #4f46e5;
        --accent-light: #eef2ff;
        --accent-mid: #c7d2fe;
        --success: #059669;
        --success-light: #ecfdf5;
        --warning: #d97706;
        --warning-light: #fffbeb;
        --danger: #dc2626;
        --danger-light: #fef2f2;
        --text-primary: #111827;
        --text-secondary: #6b7280;
        --text-muted: #9ca3af;
        --border: #e5e7eb;
        --border-strong: #d1d5db;
        --surface: #ffffff;
        --surface-raised: #f9fafb;
        --shadow-card: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.06);
        --radius: 14px;
        --radius-sm: 8px;
        --radius-xs: 6px;
    }

    * { font-family: 'DM Sans', sans-serif; }

    /* ── Card ── */
    .pd-card {
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow-card);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    /* ── Header ── */
    .pd-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 22px 28px;
        border-bottom: 1px solid var(--border);
        background: var(--surface-raised);
        gap: 16px;
        flex-wrap: wrap;
    }

    .pd-title {
        font-size: 17px;
        font-weight: 600;
        color: var(--text-primary);
        letter-spacing: -0.3px;
        margin: 0 0 10px;
    }

    /* ── Status Pills ── */
    .pd-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 500;
        letter-spacing: 0.01em;
    }

    .pd-status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .pd-status.approved { background: var(--success-light); color: var(--success); border: 1px solid #a7f3d0; }
    .pd-status.pending  { background: var(--warning-light); color: var(--warning); border: 1px solid #fde68a; }
    .pd-status.rejected { background: var(--danger-light);  color: var(--danger);  border: 1px solid #fecaca; }

    .pd-status.approved .pd-status-dot { background: var(--success); }
    .pd-status.pending  .pd-status-dot { background: var(--warning); }
    .pd-status.rejected .pd-status-dot { background: var(--danger);  }

    /* ── Action Buttons ── */
    .pd-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .pd-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.15s ease;
        cursor: pointer;
        border: none;
        letter-spacing: 0.01em;
        white-space: nowrap;
        line-height: 1;
    }

    .pd-btn svg { width: 14px; height: 14px; flex-shrink: 0; }

    .pd-btn-approve { background: var(--success); color: #fff; box-shadow: 0 1px 3px rgba(5,150,105,0.3); }
    .pd-btn-approve:hover { background: #047857; box-shadow: 0 3px 10px rgba(5,150,105,0.35); transform: translateY(-1px); color: #fff; text-decoration: none; }

    .pd-btn-reject { background: var(--danger); color: #fff; box-shadow: 0 1px 3px rgba(220,38,38,0.3); }
    .pd-btn-reject:hover { background: #b91c1c; box-shadow: 0 3px 10px rgba(220,38,38,0.35); transform: translateY(-1px); color: #fff; text-decoration: none; }

    .pd-btn-back { background: var(--surface); color: var(--text-secondary); border: 1px solid var(--border-strong); }
    .pd-btn-back:hover { background: var(--surface-raised); color: var(--text-primary); text-decoration: none; }

    /* ── Content ── */
    .pd-content { padding: 28px; }

    /* ── Info Grid ── */
    .pd-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        overflow: hidden;
    }

    @media (max-width: 480px) { .pd-info-grid { grid-template-columns: 1fr; } }

    .pd-info-cell {
        padding: 16px 20px;
        border-right: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
        transition: background 0.12s;
    }
    .pd-info-cell:hover { background: var(--surface-raised); }
    .pd-info-cell:nth-child(2n) { border-right: none; }

    @media (max-width: 480px) {
        .pd-info-cell { border-right: none; }
    }

    .pd-info-label {
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--text-muted);
        margin-bottom: 5px;
    }

    .pd-info-value {
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary);
    }

    .pd-mono {
        font-family: 'DM Mono', monospace;
        font-size: 12.5px;
        color: var(--accent);
        background: var(--accent-light);
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
    }

    /* ── Section Header ── */
    .pd-section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 32px 0 16px;
    }

    .pd-section-title {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
    }

    .pd-section-count {
        background: var(--accent-light);
        color: var(--accent);
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 999px;
    }

    .pd-section-line {
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    /* ── Variants Table ── */
    .pd-table-wrap {
        overflow-x: auto;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
    }

    .pd-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }

    .pd-table thead tr { background: var(--surface-raised); }

    .pd-table th {
        padding: 11px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    .pd-table tbody tr { border-bottom: 1px solid var(--border); transition: background 0.12s; }
    .pd-table tbody tr:last-child { border-bottom: none; }
    .pd-table tbody tr:hover { background: #fafbff; }

    .pd-table td {
        padding: 14px 16px;
        color: var(--text-primary);
        vertical-align: middle;
    }

    /* image */
    .pd-variant-img {
        width: 52px;
        height: 52px;
        border-radius: var(--radius-xs);
        object-fit: cover;
        border: 1px solid var(--border);
        box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        display: block;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .pd-variant-img:hover {
        transform: scale(1.06);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .pd-img-thumb-wrap {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }

    .pd-img-thumb-wrap:hover .pd-img-zoom-icon {
        opacity: 1;
    }

    .pd-img-zoom-icon {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0,0,0,0.35);
        border-radius: var(--radius-xs);
        opacity: 0;
        transition: opacity 0.15s ease;
        pointer-events: none;
    }

    .pd-img-zoom-icon svg {
        width: 16px;
        height: 16px;
        color: #fff;
    }

    .pd-img-placeholder {
        width: 52px;
        height: 52px;
        border-radius: var(--radius-xs);
        background: var(--surface-raised);
        border: 1px dashed var(--border-strong);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
    }
    .pd-img-placeholder svg { width: 20px; height: 20px; }

    /* variant */
    .pd-variant-name { font-weight: 500; }

    /* attributes */
    .pd-attr-list { display: flex; flex-wrap: wrap; gap: 5px; }

    .pd-attr-tag {
        background: var(--accent-light);
        color: var(--accent);
        font-size: 11.5px;
        font-weight: 500;
        padding: 3px 9px;
        border-radius: 999px;
        border: 1px solid var(--accent-mid);
        white-space: nowrap;
    }

    .pd-attr-key { opacity: 0.6; }

    /* price */
    .pd-price {
        font-family: 'DM Mono', monospace;
        font-weight: 500;
        font-size: 13px;
    }

    /* stock */
    .pd-stock { font-weight: 600; font-size: 13px; }
    .pd-stock.in-stock  { color: var(--success); }
    .pd-stock.low-stock { color: var(--warning); }
    .pd-stock.out-stock { color: var(--danger);  }

    /* business */
    .pd-biz {
        font-size: 12.5px;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 1px 0;
    }
    .pd-biz::before {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: var(--border-strong);
        flex-shrink: 0;
    }

    /* empty state */
    .pd-empty { text-align: center; padding: 48px 20px; }

    .pd-empty-icon {
        width: 48px;
        height: 48px;
        background: var(--surface-raised);
        border: 1px solid var(--border);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        color: var(--text-muted);
    }
    .pd-empty-icon svg { width: 22px; height: 22px; }
    .pd-empty-title { font-size: 14px; color: var(--text-secondary); font-weight: 500; }
    .pd-empty-sub   { font-size: 12.5px; color: var(--text-muted); margin-top: 4px; }
</style>

<div class="grid grid-cols-1 lg:gap-16 md:gap-10">

    <div class="pd-card">

        {{-- HEADER --}}
        <div class="pd-header">

            <div>
                <h2 class="pd-title">Product Details</h2>

                {{-- STATUS --}}
                @if ($product->status == 1)
                    <span class="pd-status approved">
                        <span class="pd-status-dot"></span>
                        Approved
                    </span>
                @elseif($product->status == 2)
                    <span class="pd-status pending">
                        <span class="pd-status-dot"></span>
                        Pending Approval
                    </span>
                @else
                    <span class="pd-status rejected">
                        <span class="pd-status-dot"></span>
                        Rejected
                    </span>
                @endif
            </div>

            <div class="pd-actions">

                {{-- APPROVE --}}
                @if($product->status != 1)
                    <a href="" class="pd-btn pd-btn-approve">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M13.5 4.5L6 12 2.5 8.5"/>
                        </svg>
                        Approve
                    </a>
                @endif

                {{-- REJECT --}}
                @if($product->status != 0)
                    <a href="" class="pd-btn pd-btn-reject">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 4L4 12M4 4l8 8"/>
                        </svg>
                        Reject
                    </a>
                @endif

                {{-- BACK --}}
                <a href="{{ url()->previous() }}" class="pd-btn pd-btn-back">
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 3L5 8l5 5"/>
                    </svg>
                    Back
                </a>

            </div>

        </div>

        {{-- CONTENT --}}
        <div class="pd-content">

            {{-- PRODUCT BASIC DETAILS --}}
            <div class="pd-info-grid">

                {{-- PRODUCT NAME --}}
                <div class="pd-info-cell">
                    <div class="pd-info-label">Product Name</div>
                    <div class="pd-info-value">{{ $product->name ?? '—' }}</div>
                </div>

                {{-- HSN --}}
                <div class="pd-info-cell">
                    <div class="pd-info-label">HSN Code</div>
                    <div class="pd-info-value">
                        @if($product->hsn?->hsn_code)
                            <span class="pd-mono">{{ $product->hsn->hsn_code }}</span>
                        @else
                            —
                        @endif
                    </div>
                </div>

                {{-- CATEGORY --}}
                <div class="pd-info-cell">
                    <div class="pd-info-label">Category</div>
                    <div class="pd-info-value">{{ $product->category?->name ?? '—' }}</div>
                </div>

                {{-- SUB CATEGORY --}}
                <div class="pd-info-cell">
                    <div class="pd-info-label">Sub Category</div>
                    <div class="pd-info-value">{{ $product->subCategory?->name ?? '—' }}</div>
                </div>

                {{-- SUB SUB CATEGORY --}}
                <div class="pd-info-cell">
                    <div class="pd-info-label">Sub Sub Category</div>
                    <div class="pd-info-value">{{ $product->subSubCategory?->name ?? '—' }}</div>
                </div>

            </div>

            {{-- VARIANTS --}}
            <div class="pd-section-header">
                <span class="pd-section-title">Product Variants</span>
                <span class="pd-section-count">{{ $product->variants->count() }}</span>
                <span class="pd-section-line"></span>
            </div>

            <div class="pd-table-wrap">
                <table class="pd-table">

                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Variant</th>
                            <th>SKU</th>
                            <th>Attributes</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Business</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($product->variants as $variant)

                            <tr>

                                {{-- IMAGE --}}
                                <td>
                                    @if ($variant->images->first())
                                        @php
                                            $img       = $variant->images->first();
                                            $thumbSrc  = $img->image_small ?? $img->image_medium ?? $img->image_large ?? null;
                                            $galleryId = 'gallery-variant-' . $variant->id;
                                        @endphp
                                        @if ($thumbSrc)

                                            {{-- Hidden gallery links for all sizes --}}
                                            @foreach ($variant->images as $varImg)
                                                @foreach (['image_large'] as $sizeKey)
                                                    @if (!empty($varImg->$sizeKey))
                                                        <a
                                                            href="{{ \Storage::url($varImg->$sizeKey) }}"
                                                            data-fancybox="{{ $galleryId }}"
                                                            data-caption="{{ $variant->name }} — {{ ucfirst(str_replace('image_', '', $sizeKey)) }}"
                                                            style="display:none">
                                                        </a>
                                                    @endif
                                                @endforeach
                                            @endforeach

                                            {{-- Visible thumbnail triggers first gallery item --}}
                                            <div class="pd-img-thumb-wrap" onclick="openVariantGallery('{{ $galleryId }}')">
                                                <img
                                                    src="{{ \Storage::url($thumbSrc) }}"
                                                    alt="{{ $variant->name }}"
                                                    class="pd-variant-img">
                                                <div class="pd-img-zoom-icon">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="11" cy="11" r="7"/>
                                                        <path d="M21 21l-4.35-4.35M11 8v6M8 11h6"/>
                                                    </svg>
                                                </div>
                                            </div>

                                        @else
                                            <div class="pd-img-placeholder">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                                    <path d="M21 15l-5-5L5 21"/>
                                                </svg>
                                            </div>
                                        @endif
                                    @else
                                        <div class="pd-img-placeholder">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                                <path d="M21 15l-5-5L5 21"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>

                                {{-- VARIANT --}}
                                <td>
                                    <span class="pd-variant-name">{{ $variant->name ?? '—' }}</span>
                                </td>

                                {{-- SKU --}}
                                <td>
                                    @if($variant->sku)
                                        <span class="pd-mono">{{ $variant->sku }}</span>
                                    @else
                                        <span style="color:var(--text-muted)">—</span>
                                    @endif
                                </td>

                                {{-- ATTRIBUTES --}}
                                <td>
                                    <div class="pd-attr-list">
                                        @foreach ($variant->attributes as $attribute)
                                            <span class="pd-attr-tag">
                                                <span class="pd-attr-key">{{ $attribute->attribute?->name ?? '' }}:</span>
                                                {{ $attribute->attributeValue?->value ?? '' }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>

                                {{-- PRICE --}}
                                <td>
                                    <span class="pd-price">₹{{ number_format($variant->sale_price ?? 0, 2) }}</span>
                                </td>

                                {{-- STOCK --}}
                                <td>
                                    @php
                                        $totalStock = $variant->stocks->sum('quantity');
                                        $stockClass = $totalStock > 10 ? 'in-stock' : ($totalStock > 0 ? 'low-stock' : 'out-stock');
                                    @endphp
                                    <span class="pd-stock {{ $stockClass }}">{{ $totalStock }}</span>
                                </td>

                                {{-- BUSINESS --}}
                                <td>
                                    @foreach ($variant->stocks as $stock)
                                        <div class="pd-biz">{{ $stock->business?->business_name ?? '—' }}</div>
                                    @endforeach
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7">
                                    <div class="pd-empty">
                                        <div class="pd-empty-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                                                <path d="M16 3H8L6 7h12l-2-4z"/>
                                            </svg>
                                        </div>
                                        <p class="pd-empty-title">No variants found</p>
                                        <p class="pd-empty-sub">This product has no variants configured yet.</p>
                                    </div>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.umd.js"></script>
<script>
    Fancybox.bind('[data-fancybox]', {
        animated: true,
        Toolbar: {
            display: {
                left:   ['infobar'],
                middle: [],
                right:  ['slideshow', 'download', 'close'],
            },
        },
        Images: {
            zoom: true,
        },
    });

    function openVariantGallery(galleryId) {
        const firstLink = document.querySelector('[data-fancybox="' + galleryId + '"]');
        if (firstLink) firstLink.click();
    }
</script>

@endsection
