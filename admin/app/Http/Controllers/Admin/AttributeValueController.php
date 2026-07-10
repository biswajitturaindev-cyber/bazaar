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
    // public function index()
    // {
    //     $values = AttributeValue::with('attribute')
    //         ->latest()
    //         ->paginate(10);

    //     return view('admin.attribute-values.index', compact('values'));
    // }

    // public function index()
    // {
    //     $values = AttributeValue::with([
    //         'attribute.category',
    //         'attribute.subCategory',
    //         'attribute.attributeMaster'
    //     ])
    //     ->latest()
    //     ->paginate(10);

    //     return view('admin.attribute-values.index', compact('values'));
    // }

    public function index()
    {
        $values = AttributeValue::with([
            'category',
            'subCategory',
            'attributeMaster'
        ])
        ->latest()
        ->paginate(10);

        return view('admin.attribute-values.index', compact('values'));
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
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'category_id'         => 'required|exists:categories,id',
    //         'sub_category_id'     => 'required|exists:sub_categories,id',
    //         'attribute_master_id' => 'required|exists:attribute_masters,id',
    //         'value'               => 'required|string|max:255',
    //         'color_code'          => 'nullable|string',
    //         'status'              => 'required|in:0,1',
    //     ]);

    //     $attribute = Attribute::where('category_id', $data['category_id'])
    //         ->where('sub_category_id', $data['sub_category_id'])
    //         ->where('attribute_master_id', $data['attribute_master_id'])
    //         ->first();

    //     if (!$attribute) {
    //         return back()->withInput()->withErrors([
    //             'attribute_master_id' => 'Attribute not found.'
    //         ]);
    //     }

    //     $exists = AttributeValue::where('attribute_id', $attribute->id)
    //         ->where('value', $data['value'])
    //         ->exists();

    //     if ($exists) {
    //         return back()->withInput()->withErrors([
    //             'value' => 'This value already exists for selected attribute.'
    //         ]);
    //     }

    //     AttributeValue::create([
    //         'category_id'         => $data['category_id'],
    //         'sub_category_id'     => $data['sub_category_id'],
    //         'attribute_master_id' => $data['attribute_master_id'],
    //         'attribute_id'        => $attribute->id,
    //         'value'               => $data['value'],
    //         'color_code'          => $data['color_code'] ?? null,
    //         'status'              => $data['status'],
    //     ]);

    //     return redirect()
    //         ->route('attribute-values.index')
    //         ->with('success', 'Attribute value created successfully');
    // }

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
    // public function update(Request $request, $id)
    // {
    //     $value = AttributeValue::findOrFail($id);

    //     $data = $request->validate([
    //         'attribute_id' => 'required|exists:attributes,id',
    //         'value' => 'required|string|max:255',
    //         'color_code' => 'nullable|string',
    //         'status' => 'required|in:0,1',
    //     ]);

    //     // Prevent duplicate (ignore current)
    //     $exists = AttributeValue::where('attribute_id', $data['attribute_id'])
    //         ->where('value', $data['value'])
    //         ->where('id', '!=', $id)
    //         ->exists();

    //     if ($exists) {
    //         return back()->withInput()->withErrors([
    //             'value' => 'This value already exists for selected attribute'
    //         ]);
    //     }

    //     // Detect attribute type
    //     $attribute = Attribute::find($data['attribute_id']);

    //     // If NOT color → remove color_code
    //     if (!str_contains(strtolower($attribute->name), 'color')) {
    //         $data['color_code'] = null;
    //     }

    //     // Update
    //     $value->update($data);

    //     return redirect()->route('attribute-values.index')->with('success', 'Attribute value updated successfully');
    // }

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
