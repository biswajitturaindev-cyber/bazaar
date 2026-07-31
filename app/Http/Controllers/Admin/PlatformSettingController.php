<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlatformSettingController extends Controller
{
    /**
     * Display the platform settings form.
     */
    public function edit()
    {
        $setting = PlatformSetting::firstOrCreate(
            ['id' => 1],
            [
                'platform_fee'   => 2.00,
                'settlement_fee' => 5.00,
                'status'         => true,
            ]
        );

        $businesses = Business::orderBy('business_name')->get();

        return view('admin.platform_settings.edit', compact('setting', 'businesses'));
    }

    /**
     * Update the platform settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'platform_fee'   => 'required|numeric|min:0',
            'settlement_fee' => 'required|numeric|min:0',
            'status'         => 'required|boolean',
        ]);

        $setting = PlatformSetting::firstOrCreate(['id' => 1]);

        $setting->update([
            'platform_fee'   => $request->platform_fee,
            'settlement_fee' => $request->settlement_fee,
            'status'         => $request->status,
        ]);

        return redirect()
            ->route('platform-settings.edit')
            ->with('success', 'Platform settings updated successfully.');
    }

    /**
     * update business settings
     */
    public function updateBusinesses(Request $request)
    {

        $request->validate([
            'businesses'   => 'required|array|min:1',
            'businesses.*' => 'exists:businesses,id',
        ]);

        DB::beginTransaction();

        try {

            $setting = PlatformSetting::firstOrFail();

            Business::whereIn('id', $request->businesses)->update([
                'platform_charge'           => $setting->platform_fee,
                'commission_settlement_fee' => $setting->settlement_fee,
            ]);

            DB::commit();

            return redirect()
                ->route('platform-settings.edit')
                ->with('success', 'Selected businesses updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

}
