<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $deposits = Deposit::with('business.user')
            ->latest()
            ->paginate(10);

        return view('admin.deposits.index', compact('deposits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        $deposit = Deposit::with('business.user')->findOrFail($id);
        return view('admin.deposits.edit', compact('deposit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       $request->validate([
            'status' => 'required|in:0,1,2',
            'transaction_id' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:500',
        ]);

        $deposit = Deposit::findOrFail($id);

        $deposit->update([
            'status' => $request->status,
            'transaction_id' => $request->transaction_id,
            'admin_note' => $request->remarks,
            'approved_at' => $request->status == Deposit::STATUS_APPROVED ? now() : null,
        ]);

        return redirect()
            ->route('deposits.index')
            ->with('success', 'Deposit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
