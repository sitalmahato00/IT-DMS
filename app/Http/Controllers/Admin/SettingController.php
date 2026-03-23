<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Show the settings page
     */
    public function index()
    {
        $college = College::first();
        return view('admin.settings', compact('college'));
    }

    /**
     * Update college settings
     */
    public function updateCollege(Request $request)
    {
        // Validate all fields including logo (logo is optional)
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'name_nepali' => 'nullable|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'logo' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:1000',
            'address_nepali' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'principal_name' => 'nullable|string|max:255',
            'principal_phone' => 'nullable|string|max:20',
            'principal_email' => 'nullable|email|max:255',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'registration_number' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:2000',
            'description_nepali' => 'nullable|string|max:2000',
        ]);

        $college = College::first() ?? new College();

        // Provide default values if creating new college
        if (!$college->exists) {
            $validated['name'] = $validated['name'] ?? 'My College';
        }

        // Handle logo upload only if a file was uploaded
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            // Delete old logo if exists
            if ($college->logo_path && Storage::disk('public')->exists($college->logo_path)) {
                Storage::disk('public')->delete($college->logo_path);
            }

            // Store new logo
            $logoPath = $request->file('logo')->store('college-logos', 'public');
            $validated['logo_path'] = $logoPath;
        }

        // Remove logo from validated if it wasn't processed
        unset($validated['logo']);

        $college->fill($validated)->save();

        return response()->json([
            'success' => true,
            'message' => 'College details updated successfully',
            'college' => $college
        ]);
    }

    /**
     * Delete college logo
     */
    public function deleteLogo()
    {
        $college = College::first();

        if ($college && $college->logo_path && Storage::disk('public')->exists($college->logo_path)) {
            Storage::disk('public')->delete($college->logo_path);
            $college->update(['logo_path' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Logo deleted successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No logo found to delete'
        ], 404);
    }
}
