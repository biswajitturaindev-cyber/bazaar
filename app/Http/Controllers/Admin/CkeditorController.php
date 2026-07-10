<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CkeditorController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {

            $file = $request->file('upload');

            $filename = time().'_'.$file->getClientOriginalName();

            $path = $file->storeAs('ckeditor', $filename, 'public');

            return response()->json([
                'uploaded' => 1,
                'fileName' => $filename,
                'url' => Storage::url($path),
            ]);
        }

        return response()->json([
            'uploaded' => 0,
            'error' => [
                'message' => 'Upload failed.'
            ]
        ]);
    }
}
