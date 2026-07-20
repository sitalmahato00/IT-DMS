<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class DepartmentController extends Controller
{
    public function edit()
    {
        $department = Department::first();
        return view('admin.department', compact('department'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'name_nepali' => 'nullable|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'logo' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'hero_images' => 'nullable|array',
            'hero_images.*' => 'nullable|image|max:4096',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:1000',
            'address_nepali' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'map_embed_url' => 'nullable|string|max:2000',
            'map_label' => 'nullable|string|max:255',
            'principal_name' => 'nullable|string|max:255',
            'principal_phone' => 'nullable|string|max:20',
            'principal_email' => 'nullable|email|max:255',
            'established_year' => 'nullable|integer|min:1900|max:2200',
            'registration_number' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:2000',
            'description_nepali' => 'nullable|string|max:2000',
            'programs_title' => 'nullable|string|max:255',
            'programs_title_nepali' => 'nullable|string|max:255',
            'programs_content' => 'nullable|string|max:4000',
            'programs_content_nepali' => 'nullable|string|max:4000',
            'programs_image' => 'nullable|image|max:4096',
        ]);

        $department = Department::first() ?? new Department();

        if (!$department->exists) {
            $validated['name'] = $validated['name'] ?? 'My Department';
        }

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            if ($department->logo_path && Storage::disk('public')->exists($department->logo_path)) {
                Storage::disk('public')->delete($department->logo_path);
            }

            $validated['logo_path'] = $request->file('logo')->store('department-logos', 'public');
        }

        unset($validated['logo']);

        if ($request->hasFile('hero_images')) {
            $files = collect(Arr::wrap($request->file('hero_images')))
                ->filter(fn ($f) => $f && $f->isValid())
                ->values();

            if ($files->isNotEmpty()) {
                $newPaths = $files
                    ->map(fn ($f) => $f->store('department-hero', 'public'))
                    ->values()
                    ->all();

                $oldPaths = collect($department->hero_images ?? [])->filter()->values()->all();
                $validated['hero_images'] = collect(array_merge($oldPaths, $newPaths))
                    ->filter()
                    ->values()
                    ->all();
            }
        }

        if ($request->hasFile('programs_image') && $request->file('programs_image')->isValid()) {
            if ($department->programs_image_path && Storage::disk('public')->exists($department->programs_image_path)) {
                Storage::disk('public')->delete($department->programs_image_path);
            }
            $validated['programs_image_path'] = $request->file('programs_image')->store('department-programs', 'public');
        }

        unset($validated['programs_image']);

        $department->fill($validated)->save();

        // Clear caches
        Cache::tags(['department'])->flush();
        Cache::forget('department:shared-current:v1');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Department details updated successfully',
                'department' => $department->fresh(),
            ]);
        }

        return redirect()->route('admin.settings')->with('status', 'Department details updated successfully');
    }

    public function deleteLogo()
    {
        $department = Department::first();

        if ($department && $department->logo_path && Storage::disk('public')->exists($department->logo_path)) {
            Storage::disk('public')->delete($department->logo_path);
            $department->update(['logo_path' => null]);
            Cache::tags(['department'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Logo deleted successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No logo found to delete',
        ], 404);
    }

    public function deleteHeroImage($index)
    {
        $department = Department::first();

        if (!$department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        }

        $heroImages = collect($department->hero_images ?? [])->filter()->values();
        $index = (int) $index;

        if (!isset($heroImages[$index])) {
            return response()->json([
                'success' => false,
                'message' => 'Hero image not found',
            ], 404);
        }

        $path = $heroImages[$index];

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $updatedImages = $heroImages
            ->reject(fn ($_, $key) => (int) $key === $index)
            ->values()
            ->all();

        $department->update(['hero_images' => $updatedImages]);

        return response()->json([
            'success' => true,
            'message' => 'Hero image deleted successfully',
            'hero_images' => $updatedImages,
        ]);
    }
}

