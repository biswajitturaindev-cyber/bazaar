<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hsn;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HsnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $hsns = Hsn::latest()->paginate(10);
            return view('admin.hsn.index', compact('hsns'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong');
            // Optional (for debugging)
            // return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       return view('admin.hsn.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'hsn_code' => 'required|string|max:20|unique:hsns,hsn_code,NULL,id,deleted_at,NULL',
                'description' => 'nullable|string',
                'cgst' => 'required|numeric|min:0',
                'sgst' => 'required|numeric|min:0',
                'igst' => 'required|numeric|min:0',
                'status' => 'required|boolean',
            ]);

            Hsn::create($data);

            return redirect()->route('hsns.index')
                ->with('success', 'HSN Created Successfully');

        } catch (ValidationException $e) {

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (Exception $e) {

            return redirect()->back()
                ->with('error', 'Something went wrong')
                ->withInput();

            // Optional debug:
            // ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $hsn = Hsn::findOrFail($id);
        // return view('admin.hsn.show', compact('hsn'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $hsn = Hsn::findOrFail($id);
            return view('admin.hsn.edit', compact('hsn'));

        } catch (ModelNotFoundException $e) {
            return redirect()->route('hsns.index')
                ->with('error', 'HSN not found');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong');
            // Optional debug:
            // ->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
                $hsn = Hsn::findOrFail($id);

                $data = $request->validate([
                    'hsn_code' => 'required|string|max:20|unique:hsns,hsn_code,' . $id . ',id,deleted_at,NULL',
                    'description' => 'nullable|string',
                    'cgst' => 'required|numeric|min:0',
                    'sgst' => 'required|numeric|min:0',
                    'igst' => 'required|numeric|min:0',
                    'status' => 'required|boolean',
                ]);

                $hsn->update($data);

                return redirect()->route('hsns.index')
                    ->with('success', 'HSN Updated Successfully');

            } catch (ModelNotFoundException $e) {

                return redirect()->route('hsns.index')
                    ->with('error', 'HSN not found');

            } catch (ValidationException $e) {

                return redirect()->back()
                    ->withErrors($e->errors())
                    ->withInput();

            } catch (Exception $e) {

                return redirect()->back()
                    ->with('error', 'Something went wrong')
                    ->withInput();

                // Optional debug:
                // ->with('error', $e->getMessage());
            }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $hsn = Hsn::findOrFail($id);
            $hsn->delete(); // soft delete

            return redirect()->route('hsns.index')
                ->with('success', 'HSN Deleted Successfully');

        } catch (ModelNotFoundException $e) {

            return redirect()->route('admin.load.product.list')
                ->with('error', 'HSN not found');

        } catch (Exception $e) {

            return redirect()->back()
                ->with('error', 'Something went wrong');

            // Optional debug:
            // ->with('error', $e->getMessage());
        }
    }

}
