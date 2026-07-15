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
    public function index(Request $request)
    {
        try {

            if ($request->ajax()) {

                $columns = [
                    0 => 'id',
                    1 => 'vendor_id',
                    2 => 'name',
                    3 => 'email',
                    4 => 'mobile',
                    5 => 'gender',
                    6 => 'status',
                    7 => 'kyc_status',
                ];

                $totalData = User::count();
                $totalFiltered = $totalData;

                $limit = $request->input('length');
                $start = $request->input('start');
                $order = $columns[$request->input('order.0.column')] ?? 'id';
                $dir = $request->input('order.0.dir', 'desc');

                $query = User::with('business');

                // Search
                if ($search = $request->input('search.value')) {

                    $query->where(function ($q) use ($search) {

                        $q->where('vendor_id', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%");

                    });

                }

                $totalFiltered = $query->count();

                $users = $query
                    ->orderBy($order, $dir)
                    ->offset($start)
                    ->limit($limit)
                    ->get();

                $data = [];

                foreach ($users as $user) {

                    // Gender
                    $gender = match ($user->gender) {
                        1 => 'Male',
                        2 => 'Female',
                        3 => 'Others',
                        default => '-',
                    };

                    // Status
                    $status = $user->status
                        ? '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700">Active</span>'
                        : '<span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">Inactive</span>';

                    // KYC
                    switch ($user->kyc_status) {
                        case 1:
                            $kyc = '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700">Approved</span>';
                            break;

                        case 2:
                            $kyc = '<span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-700">Pending</span>';
                            break;

                        case 3:
                            $kyc = '<span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">Cancelled</span>';
                            break;

                        default:
                            $kyc = '<span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-700">Not Submitted</span>';
                    }

                    // Admin Shop
                    if ($user->business) {

                        $shop = '<span class="px-2 py-1 text-xs font-semibold rounded ' .
                            ($user->business->admin_shop_status == 'open'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-red-100 text-red-700') .
                            '">' .
                            ucfirst($user->business->admin_shop_status) .
                            '</span>';

                    } else {

                        $shop = '<span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-700">
                                    N/A
                                </span>';
                    }

                    // Action
                    $action = '
                        <div class="flex gap-2">

                            <a href="' . route('users.edit', $user->id) . '"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                Edit
                            </a>';

                    if ($user->business?->id) {

                        $action .= '
                            <a href="' . route('vendors.products.index', $user->business->id) . '"
                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded flex items-center gap-1"
                                title="Show products">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-4 h-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 8h14l-1 12H6L5 8zm4-3a3 3 0 016 0" />

                                </svg>

                            </a>';
                    }

                    $action .= '</div>';

                    $data[] = [
                        '',
                        $user->vendor_id,
                        $user->name,
                        $user->email,
                        $user->mobile,
                        $gender,
                        $status,
                        $kyc,
                        $shop,
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

            return view('admin.users.index');

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
