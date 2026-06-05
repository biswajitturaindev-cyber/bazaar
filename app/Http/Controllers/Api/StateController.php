<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StateResource;
use Illuminate\Http\Request;
use App\Models\State;
class StateController extends Controller
{
    public function index()
    {
        $states = State::orderBy('name')->get();

        return response()->json([
            'status' => true,
            'message' => 'States fetched successfully.',
            'data' => StateResource::collection($states),
        ]);
    }
}
