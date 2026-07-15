<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Models\Attribute;
use App\Models\AttributeMaster;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Validation\Rule;

class AttributeValueController extends Controller
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
                3 => 'attribute_master_id',
                4 => 'value',
                5 => 'status',
            ];

            $totalData = AttributeValue::count();
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')] ?? 'id';
            $dir = $request->input('order.0.dir', 'desc');

            $query = AttributeValue::with([
                'category',
                'subCategory',
                'attributeMaster'
            ]);

            // Search
            if ($search = $request->input('search.value')) {

                $query->where(function ($q) use ($search) {

                    $q->where('value', 'like', "%{$search}%")
                        ->orWhere('color_code', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($cat) use ($search) {
                            $cat->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('subCategory', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('attributeMaster', function ($master) use ($search) {
                            $master->where('name', 'like', "%{$search}%");
                        });

                });

            }

            $totalFiltered = $query->count();

            $values = $query
                ->orderBy($order, $dir)
                ->offset($start)
                ->limit($limit)
                ->get();

            $data = [];

            foreach ($values as $value) {

                $valueText = '<div class="flex items-center gap-2">';

                if ($value->color_code) {
                    $valueText .= '
                        <span class="w-5 h-5 rounded border"
                            style="display:inline-block;background-color:' . $value->color_code . ';"></span>';
                }

                $valueText .= '<span>' . ($value->value ?? '-') . '</span>';

                if ($value->color_code) {
                    $valueText .= '
                        <span class="text-xs text-gray-500">
                            (' . $value->color_code . ')
                        </span>';
                }

                $valueText .= '</div>';

                $status = $value->status == 1
                    ? '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700">Active</span>'
                    : '<span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">Inactive</span>';

                $action = '
                    <a href="' . route('attribute-values.edit', $value->id) . '"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                        Edit
                    </a>';

                $data[] = [
                    '',
                    $value->category?->name ?? '-',
                    $value->subCategory?->name ?? '-',
                    $value->attributeMaster?->name ?? '-',
                    $valueText,
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

        return view('admin.attribute-values.index');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $attributes = Attribute::where('status', 1)->get();
        return view('admin.attribute-values.create', compact('attributes','categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'attribute_master_id' => 'required|exists:attribute_masters,id',

            'value' => [
                'required',
                'string',
                'max:255',
                Rule::unique('attribute_values')->where(function ($query) use ($request) {
                    return $query->where('category_id', $request->category_id)
                        ->where('sub_category_id', $request->sub_category_id)
                        ->where('attribute_master_id', $request->attribute_master_id);
                }),
            ],

            'color_code' => 'nullable|string|max:50',
            'status' => 'required|in:0,1',
        ], [
            'value.unique' => 'This value already exists.',
        ]);

        AttributeValue::create([
            'category_id' => $data['category_id'],
            'sub_category_id' => $data['sub_category_id'],
            'attribute_master_id' => $data['attribute_master_id'],
            'value' => $data['value'],
            'color_code' => $data['color_code'] ?? null,
            'status' => $data['status'],
        ]);

        return redirect()
            ->route('attribute-values.index')
            ->with('success', 'Attribute value created successfully.');
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
        $value = AttributeValue::findOrFail($id);

        $categories = Category::where('status', 1)->get();

        $subCategories = SubCategory::where(
            'category_id',
            $value->category_id
        )->get();

        $attributeMasters = AttributeMaster::join(
                'attributes',
                'attributes.attribute_master_id',
                '=',
                'attribute_masters.id'
            )
            ->where('attributes.category_id', $value->category_id)
            ->where('attributes.sub_category_id', $value->sub_category_id)
            ->select('attribute_masters.*')
            ->distinct()
            ->get();

        return view(
            'admin.attribute-values.edit',
            compact(
                'value',
                'categories',
                'subCategories',
                'attributeMasters'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $value = AttributeValue::findOrFail($id);

        $data = $request->validate([
            'category_id'         => 'required|exists:categories,id',
            'sub_category_id'     => 'required|exists:sub_categories,id',
            'attribute_master_id' => 'required|exists:attribute_masters,id',
            'value'               => 'required|string|max:255',
            'color_code'          => 'nullable|string',
            'status'              => 'required|in:0,1',
        ]);

        $attribute = Attribute::where('category_id', $data['category_id'])
            ->where('sub_category_id', $data['sub_category_id'])
            ->where('attribute_master_id', $data['attribute_master_id'])
            ->first();

        if (!$attribute) {
            return back()->withInput()->withErrors([
                'attribute_master_id' => 'Attribute not found.'
            ]);
        }

        // Check duplicate value
        $exists = AttributeValue::where('attribute_id', $attribute->id)
            ->where('value', $data['value'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'value' => 'This value already exists for selected attribute.'
            ]);
        }

        // If attribute is not Color, remove color_code
        if (!str_contains(strtolower($attribute->name), 'color')) {
            $data['color_code'] = null;
        }

        $value->update([
            'category_id'         => $data['category_id'],
            'sub_category_id'     => $data['sub_category_id'],
            'attribute_master_id' => $data['attribute_master_id'],
            'attribute_id'        => $attribute->id,
            'value'               => $data['value'],
            'color_code'          => $data['color_code'] ?? null,
            'status'              => $data['status'],
        ]);

        return redirect()
            ->route('attribute-values.index')
            ->with('success', 'Attribute value updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $value = AttributeValue::findOrFail($id);
            $value->delete();

            return redirect()->back()->with('success', 'Deleted successfully');

        } catch (\Exception $e) {

            return back()->withErrors([
                'error' => 'Delete failed'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function getSubCategories(Request $request)
    {
        $subCategories = Attribute::join(
                'sub_categories',
                'sub_categories.id',
                '=',
                'attributes.sub_category_id'
            )
            ->where('attributes.category_id', $request->category_id)
            ->select('sub_categories.id', 'sub_categories.name')
            ->distinct()
            ->get();

        return response()->json($subCategories);
    }

    public function getAttributeMaster(Request $request)
    {
        $attributeMasters = AttributeMaster::select(
                'attribute_masters.id',
                'attribute_masters.name'
            )
            ->join(
                'attributes',
                'attributes.attribute_master_id',
                '=',
                'attribute_masters.id'
            )
            ->where('attributes.category_id', $request->category_id)
            ->where('attributes.sub_category_id', $request->sub_category_id)
            ->distinct()
            ->get();

        return response()->json($attributeMasters);
    }

}
