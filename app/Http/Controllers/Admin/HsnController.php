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
    public function index(Request $request)
    {
        try {

            if ($request->ajax()) {

                $columns = [
                    0 => 'id',
                    1 => 'hsn_code',
                    2 => 'description',
                    3 => 'cgst',
                    4 => 'sgst',
                    5 => 'igst',
                    6 => 'status',
                ];

                $totalData = Hsn::count();
                $totalFiltered = $totalData;

                $limit = $request->input('length');
                $start = $request->input('start');
                $order = $columns[$request->input('order.0.column')] ?? 'id';
                $dir = $request->input('order.0.dir', 'desc');

                $query = Hsn::query();

                // Search
                if ($search = $request->input('search.value')) {

                    $query->where(function ($q) use ($search) {

                        $q->where('hsn_code', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhere('cgst', 'like', "%{$search}%")
                            ->orWhere('sgst', 'like', "%{$search}%")
                            ->orWhere('igst', 'like', "%{$search}%");

                    });

                }

                $totalFiltered = $query->count();

                $hsns = $query
                    ->orderBy($order, $dir)
                    ->offset($start)
                    ->limit($limit)
                    ->get();

                $data = [];

                foreach ($hsns as $hsn) {

                    $status = $hsn->status
                        ? '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700">Active</span>'
                        : '<span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">Inactive</span>';

                    $action = '
                        <a href="' . route('hsns.edit', $hsn->id) . '"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                            Edit
                        </a>';

                    $data[] = [
                        '',
                        $hsn->hsn_code,
                        $hsn->description ?? '-',
                        $hsn->cgst . '%',
                        $hsn->sgst . '%',
                        $hsn->igst . '%',
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

            return view('admin.hsn.index');

        } catch (Exception $e) {

            return redirect()->back()->with('error', 'Something went wrong');
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
