<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $columns = [
                0 => 'id',
                1 => 'title',
                2 => 'banner_type',
                3 => 'sort_order',
                4 => 'status',
            ];

            $totalData = Banner::count();
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')] ?? 'id';
            $dir = $request->input('order.0.dir', 'desc');

            $query = Banner::query();

            // Search
            if ($search = $request->input('search.value')) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                    ->orWhere('banner_type', 'like', "%{$search}%");
                });
            }

            $totalFiltered = $query->count();

            $banners = $query
                ->orderBy($order, $dir)
                ->offset($start)
                ->limit($limit)
                ->get();

            $data = [];

            foreach ($banners as $banner) {

                $image = $banner->image
                    ? '<img src="' . asset('storage/' . $banner->image) . '" class="w-16 h-12 rounded border object-cover">'
                    : '<img src="' . asset('images/no-image.png') . '" class="w-16 h-12 rounded border object-cover">';

                $bannerType = match ($banner->banner_type) {
                    'promotional_banner' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-700">Promotional Banner</span>',
                    'advertisement_banner' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-700">Advertisement Banner</span>',
                    default => '-',
                };

                $status = $banner->status
                    ? '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700">Active</span>'
                    : '<span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">Inactive</span>';

                $action = '
                <div class="flex items-center gap-2">
                    <a href="' . route('banners.edit', $banner->id) . '"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                        Edit
                    </a>

                    <button
                        class="delete-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
                        data-url="' . route('banners.destroy', $banner->id) . '">
                        Delete
                    </button>
                </div>';

                $data[] = [
                    '',
                    $banner->title,
                    $bannerType,
                    $image,
                    $banner->sort_order,
                    $status,
                    $action,
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data,
            ]);
        }

        return view('admin.banner.index');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banner.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'banner_type' => 'required|in:promotional_banner,advertisement_banner',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'sort_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        $imagePath = null;

        try {

            if ($request->hasFile('image')) {

                $manager = new ImageManager(new Driver());

                $file = $request->file('image');

                $filename = time() . '_' . uniqid();

                // Resize and convert to WebP
                $banner = $manager->read($file)->cover(1200, 600);

                $imagePath = "banners/{$filename}.webp";

                Storage::disk('public')->put(
                    $imagePath,
                    compressToTargetSize($banner, 80)
                );
            }

            Banner::create([
                'title'       => $request->title,
                'banner_type' => $request->banner_type,
                'image'       => $imagePath,
                'sort_order'  => $request->sort_order,
                'status'      => $request->status,
            ]);

            DB::commit();

            return redirect()
                ->route('banners.index')
                ->with('success', 'Banner created successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {

            $banner = Banner::findOrFail($id);

            return view('admin.banner.edit', compact('banner'));

        } catch (\Exception $e) {

            return redirect()
                ->route('banners.index')
                ->with('error', 'Banner not found.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'banner_type' => 'required|in:promotional_banner,advertisement_banner',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'sort_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

            $banner = Banner::findOrFail($id);

            if ($request->hasFile('image')) {

                $manager = new ImageManager(new Driver());
                $file = $request->file('image');
                $filename = time() . '_' . uniqid();
                // Resize and convert to WebP
                $image = $manager->read($file)->cover(1200, 600);
                $imagePath = "banners/{$filename}.webp";
                Storage::disk('public')->put(
                    $imagePath,
                    compressToTargetSize($image, 80)
                );

                // Delete old image after successful upload
                if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                    Storage::disk('public')->delete($banner->image);
                }
                $banner->image = $imagePath;
            }

            $banner->title = $request->title;
            $banner->banner_type = $request->banner_type;
            $banner->sort_order = $request->sort_order;
            $banner->status = $request->status;

            $banner->save();

            DB::commit();

            return redirect()
                ->route('banners.index')
                ->with('success', 'Banner updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {

            $banner = Banner::findOrFail($id);

            // Delete banner image if it exists
            if (!empty($banner->image) && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }

            $banner->delete();

            DB::commit();

            return redirect()
                ->route('banners.index')
                ->with('success', 'Banner deleted successfully.');

        } catch (\Throwable $e) {

            DB::rollBack();

            return redirect()
                ->route('banners.index')
                ->with('error', 'Something went wrong. ' . $e->getMessage());
        }
    }
}
