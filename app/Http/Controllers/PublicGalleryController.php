<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;

class PublicGalleryController extends Controller
{
    public function download($id)
    {
        $item = Gallery::active()->findOrFail($id);

        if (empty($item->image_path) || !Storage::disk('public')->exists($item->image_path)) {
            abort(404);
        }

        $downloadName = $item->image_name ?: basename($item->image_path);

        return Storage::disk('public')->download($item->image_path, $downloadName);
    }
}


