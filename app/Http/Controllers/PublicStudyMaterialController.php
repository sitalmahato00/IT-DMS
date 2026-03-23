<?php

namespace App\Http\Controllers;

use App\Models\StudyMaterial;
use Illuminate\Support\Facades\Storage;

class PublicStudyMaterialController extends Controller
{
    public function download($id)
    {
        $material = StudyMaterial::published()
            ->where('visibility', 'all')
            ->findOrFail($id);

        if (empty($material->file_path) || !Storage::disk('public')->exists($material->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $material->file_path,
            $material->file_name ?: basename($material->file_path)
        );
    }
}

