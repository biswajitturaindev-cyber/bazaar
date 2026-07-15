<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttributeMaster;
use App\Models\BusinessCategory;
use App\Models\BusinessSubCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class AttributemasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $columns = [
                0 => 'id',
                1 => 'category_id',
                2 => 'sub_category_id',
                3 => 'name',
            ];

            $totalData = AttributeMaster::count();
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')] ?? 'id';
            $dir = $request->input('order.0.dir', 'desc');

            $query = AttributeMaster::with(['category', 'subCategory']);

            // Search
            if ($search = $request->input('search.value')) {

                $query->where(function ($q) use ($search) {

                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($cat) use ($search) {
                            $cat->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('subCategory', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%");
                        });

                });

            }

            $totalFiltered = $query->count();

            $masters = $query
                ->orderBy($order, $dir)
                ->offset($start)
                ->limit($limit)
                ->get();

            $data = [];

            foreach ($masters as $master) {

                $action = '
                    <div class="flex gap-2">
                        <a href="' . route('attribute-master.edit', $master->id) . '"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                            Edit
                        </a>

                        <form action="' . route('attribute-master.destroy', $master->id) . '"
                            method="POST"
                            onsubmit="return confirm(\'Are you sure?\')"
                            style="display:inline-block;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                Delete
                            </button>
                        </form>
                    </div>';

                $data[] = [
                    '',
                    $master->category?->name ?? '-',
                    $master->subCategory?->name ?? '-',
                    $master->name,
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

        return view('admin.attribute-master.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = BusinessCategory::where('status', 1)->get();
        $subCategories = BusinessSubCategory::where('status', 1)->get();

        return view('admin.attribute-master.create', compact(
            'categories',
            'subCategories'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'business_category_id' => 'required|integer',
            'business_sub_category_id' => 'required|integer',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('attribute_masters', 'name')
                    ->where(function ($query) use ($request) {
                        return $query->where('business_category_id', $request->business_category_id)
                                    ->where('business_sub_category_id', $request->business_sub_category_id);
                    }),
            ],
        ]);

        AttributeMaster::create($data);

        return redirect()->route('attribute-master.index')
            ->with('success', 'Attribute Master created successfully');
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
        $master = AttributeMaster::findOrFail($id);
        $categories = BusinessCategory::where('status', 1)->get();

        $subCategories = BusinessSubCategory::where('business_category_id', $master->business_category_id)
            ->where('status', 1)
            ->get();

        return view('admin.attribute-master.edit', compact(
            'master',
            'categories',
            'subCategories'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $master = AttributeMaster::findOrFail($id);

        $data = $request->validate([
            'business_category_id' => 'required|integer',
            'business_sub_category_id' => 'required|integer',
            'name' => 'required|string|max:255|unique:attribute_masters,name,' . $id,
        ]);

        $master->update($data);

        return redirect()->route('attribute-master.index')
            ->with('success', 'Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $master = AttributeMaster::findOrFail($id);
        $master->delete();

        return back()->with('success', 'Deleted successfully');
    }
}
