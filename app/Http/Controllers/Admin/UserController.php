<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::with('business')->latest()->paginate(10);
            return view('admin.users.index', compact('users'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
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
        try {
            $user = User::with('latestSubscription')->findOrFail($id);
            $packages = Package::where('status', 1)->get();

            return view('admin.users.edit', compact('user', 'packages'));

        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'User not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        try {

            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'mobile' => 'nullable',
                'dob' => 'nullable|date',
                'kyc_status' => 'required|in:0,1,2,3',
                'status' => 'required|in:0,1',
                'gender' => 'required|in:1,2,3',
                'package_id' => 'nullable|exists:packages,id',

                // Business
                'admin_shop_status' => 'nullable|in:open,closed',
                'shop_status' => 'nullable|in:open,closed',
                'working_days' => 'nullable|array',
                'working_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',

            ]);

            // Update user
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'dob' => $validated['dob'],
                'kyc_status' => $validated['kyc_status'],
                'status' => $validated['status'],
                'gender' => $validated['gender'],
            ]);

            // Update business details
            if ($user->business) {
                $user->business->update([
                    'admin_shop_status' => $validated['admin_shop_status'] ?? $user->business->admin_shop_status,
                    'shop_status' => $validated['shop_status'] ?? $user->business->shop_status,
                    'working_days' => $validated['working_days'] ?? $user->business->working_days,
                ]);
            }

            // Package update
            if (!empty($validated['package_id'])) {

                $package = Package::findOrFail($validated['package_id']);

                $startDate = Carbon::today();

                $endDate = match ($package->duration_type) {
                    'day'   => $startDate->copy()->addDays($package->duration),
                    'month' => $startDate->copy()->addMonths($package->duration),
                    'year'  => $startDate->copy()->addYears($package->duration),
                };

                // Active subscription
                $subscription = UserSubscription::where('user_id', $user->id)
                    ->where('status', 1)
                    ->whereDate('end_date', '>=', Carbon::today())
                    ->latest()
                    ->first();

                if ($subscription) {

                    // Update existing subscription
                    $subscription->update([
                        'package_id' => $package->id,
                        'amount' => $package->price,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'payment_status' => 'paid',
                        'payment_method' => '',
                        'transaction_id' => null,
                    ]);

                } else {

                    // Create new subscription
                    UserSubscription::create([
                        'user_id' => $user->id,
                        'package_id' => $package->id,
                        'amount' => $package->price,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'payment_status' => 'paid',
                        'payment_method' => '',
                        'transaction_id' => null,
                        'status' => 1,
                    ]);
                }
            }




            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
