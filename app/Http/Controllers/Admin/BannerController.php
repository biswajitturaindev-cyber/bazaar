<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                2 => 'sort_order',
                3 => 'status',
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
                $query->where('title', 'like', "%{$search}%");
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
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'sort_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('banners', 'public');
            }

            Banner::create([
                'title' => $request->title,
                'image' => $imagePath,
                'sort_order' => $request->sort_order,
                'status' => $request->status,
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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'sort_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {

            $banner = Banner::findOrFail($id);

            if ($request->hasFile('image')) {

                // Delete old image
                if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                    Storage::disk('public')->delete($banner->image);
                }

                // Upload new image
                $banner->image = $request->file('image')->store('banners', 'public');
            }

            $banner->title = $request->title;
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

            // Delete image
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }

            $banner->delete();

            DB::commit();

            return redirect()
                ->route('banners.index')
                ->with('success', 'Banner deleted successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->route('banners.index')
                ->with('error', 'Something went wrong. ' . $e->getMessage());
        }
    }
}
