<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function index()
    {
        // Get current settings
        $settings = $this->getSettings();
        return view('admin.settings', compact('settings'));
    }

    private function getSettings()
    {
        $defaults = [
            'brand_name' => 'Brew & Bean Co.',
            'brand_tagline' => 'Management System',
        ];

        $settings = [];
        foreach ($defaults as $key => $default) {
            $settings[$key] = Storage::disk('public')->exists('settings/' . $key . '.txt') 
                ? Storage::disk('public')->get('settings/' . $key . '.txt') 
                : $default;
        }

        return $settings;
    }

    public function updateBrand(Request $request)
    {
        try {
            $request->validate([
                'brand_name' => 'required|string|max:100',
                'brand_tagline' => 'nullable|string|max:100',
            ]);

            // Save brand name
            Storage::disk('public')->put('settings/brand_name.txt', $request->brand_name);
            
            // Save brand tagline
            Storage::disk('public')->put('settings/brand_tagline.txt', $request->brand_tagline ?? '');

            return response()->json([
                'success' => true,
                'message' => 'Brand updated successfully!',
                'brand_name' => $request->brand_name,
                'brand_tagline' => $request->brand_tagline
            ]);

        } catch (\Exception $e) {
            Log::error('Brand update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating brand: ' . $e->getMessage()
            ], 400);
        }
    }

    public function updateLogo(Request $request)
    {
        try {
            $request->validate([
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120'
            ]);

            // Delete old logo
            if (Storage::disk('public')->exists('settings/logo.png')) {
                Storage::disk('public')->delete('settings/logo.png');
            }

            // Get the uploaded file and save directly
            $image = $request->file('logo');
            $imageName = 'logo.png';
            
            // Save the image directly to storage
            $image->storeAs('settings', $imageName, 'public');

            // Get final logo path
            $logoPath = Storage::url('settings/' . $imageName);

            return response()->json([
                'success' => true,
                'message' => 'Logo updated successfully!',
                'path' => $logoPath,
                'size' => $this->getFileSize('settings/' . $imageName)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . ($e->errors()['logo'][0] ?? 'Invalid file')
            ], 422);
        } catch (\Exception $e) {
            Log::error('Logo update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating logo: ' . $e->getMessage()
            ], 400);
        }
    }

    private function getFileSize($path)
    {
        try {
            $size = Storage::disk('public')->size($path);
            if ($size < 1024) {
                return $size . ' B';
            } elseif ($size < 1048576) {
                return round($size / 1024, 1) . ' KB';
            } else {
                return round($size / 1048576, 2) . ' MB';
            }
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    public function removeLogo()
    {
        try {
            if (Storage::disk('public')->exists('settings/logo.png')) {
                Storage::disk('public')->delete('settings/logo.png');
            }

            return response()->json([
                'success' => true,
                'message' => 'Logo removed successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing logo: ' . $e->getMessage()
            ], 400);
        }
    }

    public function getLogo()
    {
        if (Storage::disk('public')->exists('settings/logo.png')) {
            $size = $this->getFileSize('settings/logo.png');
            return response()->json([
                'exists' => true,
                'path' => Storage::url('settings/logo.png'),
                'size' => $size
            ]);
        }

        return response()->json([
            'exists' => false,
            'path' => null
        ]);
    }
}