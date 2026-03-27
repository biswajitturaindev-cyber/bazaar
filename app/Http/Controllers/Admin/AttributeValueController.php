<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Models\Attribute;

class AttributeValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $values = AttributeValue::with('attribute')
            ->latest()
            ->paginate(10);

        return view('admin.attribute-values.index', compact('values'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $attributes = Attribute::where('status', 1)->get();
        return view('admin.attribute-values.create', compact('attributes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required|string|max:255',
            'color_code' => 'nullable|string',
            'status' => 'required|in:0,1',
        ]);

        $exists = AttributeValue::where('attribute_id', $data['attribute_id'])
            ->where('value', $data['value'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'value' => 'This value already exists for selected attribute'
            ]);
        }

        AttributeValue::create([
            'attribute_id' => $data['attribute_id'],
            'value' => $data['value'],
            'color_code' => $data['color_code'],
            'status' => $data['status'],
        ]);

        return redirect()->route('attribute-values.index')
            ->with('success', 'Attribute value created successfully');
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
        $attributes = Attribute::where('status', 1)->get();

        return view('admin.attribute-values.edit', compact('value', 'attributes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $value = AttributeValue::findOrFail($id);

        $data = $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required|string|max:255',
            'color_code' => 'nullable|string',
            'status' => 'required|in:0,1',
        ]);

        // Prevent duplicate (ignore current)
        $exists = AttributeValue::where('attribute_id', $data['attribute_id'])
            ->where('value', $data['value'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'value' => 'This value already exists for selected attribute'
            ]);
        }

        // Detect attribute type
        $attribute = Attribute::find($data['attribute_id']);

        // If NOT color → remove color_code
        if (!str_contains(strtolower($attribute->name), 'color')) {
            $data['color_code'] = null;
        }

        // Update
        $value->update($data);

        return redirect()->route('attribute-values.index')->with('success', 'Attribute value updated successfully');
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
}
