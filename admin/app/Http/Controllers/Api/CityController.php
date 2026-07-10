<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use Illuminate\Http\Request;
use App\Models\City;
use Vinkla\Hashids\Facades\Hashids;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::orderBy('name')->get();

        return response()->json([
            'status' => true,
            'message' => 'Cities fetched successfully.',
            'data' => CityResource::collection($cities),
        ]);
    }

    public function getCitiesByState($state_id)
    {
        try {

            $decoded = Hashids::decode($state_id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid state ID'
                ], 400);
            }

            $stateId = $decoded[0];

            $cities = City::where('state_id', $stateId)
                ->orderBy('name')
                ->get();

            return response()->json([
                'status' => true,
                'data' => CityResource::collection($cities)
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
