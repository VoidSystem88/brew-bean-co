<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::orderBy('name')->get();
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location' => 'required|string|max:100',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // If no coordinates provided, auto-geocode from address
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            
            if (empty($latitude) || empty($longitude)) {
                $coords = $this->geocodeAddress($request->address);
                if ($coords) {
                    $latitude = $coords['lat'];
                    $longitude = $coords['lng'];
                }
            }

            $branch = Branch::create([
                'name' => '☕ Brew & Bean Co. - ' . $request->location,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'is_active' => $request->is_active ?? true,
            ]);

            return redirect()->route('branches.index')
                ->with('success', 'Branch "' . $branch->name . '" created successfully!');
                
        } catch (\Exception $e) {
            Log::error('Branch creation error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating branch: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Branch $branch)
    {
        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        $location = str_replace('☕ Brew & Bean Co. - ', '', $branch->name);
        return view('branches.edit', compact('branch', 'location'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validator = Validator::make($request->all(), [
            'location' => 'required|string|max:100',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // If no coordinates provided, auto-geocode from address
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            
            if (empty($latitude) || empty($longitude)) {
                $coords = $this->geocodeAddress($request->address);
                if ($coords) {
                    $latitude = $coords['lat'];
                    $longitude = $coords['lng'];
                }
            }

            $branch->update([
                'name' => '☕ Brew & Bean Co. - ' . $request->location,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'is_active' => $request->is_active ?? true,
            ]);

            return redirect()->route('branches.index')
                ->with('success', 'Branch "' . $branch->name . '" updated successfully!');
                
        } catch (\Exception $e) {
            Log::error('Branch update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating branch: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Branch $branch)
    {
        try {
            $branchName = $branch->name;
            
            if ($branch->sales()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete "' . $branchName . '" because it has sales records.'
                ], 400);
            }
            
            $branch->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Branch "' . $branchName . '" deleted successfully!'
            ]);
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    // Auto-geocode address using OpenStreetMap Nominatim (FREE)
    private function geocodeAddress($address)
    {
        if (empty($address)) {
            return null;
        }

        try {
            $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($address) . "&format=json&limit=1";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'BrewBeanCo/1.0');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            curl_close($ch);
            
            $data = json_decode($response, true);
            
            if ($data && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                return [
                    'lat' => floatval($data[0]['lat']),
                    'lng' => floatval($data[0]['lon'])
                ];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Geocoding error: ' . $e->getMessage());
            return null;
        }
    }

    // Search location for autocomplete
    public function searchLocation(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }
        
        // Use OpenStreetMap Nominatim API
        $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($query) . "&format=json&addressdetails=1&limit=10&countrycodes=ph";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'BrewBeanCo/1.0');
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        
        $results = [];
        if ($data) {
            foreach ($data as $item) {
                $results[] = [
                    'display_name' => $item['display_name'] ?? '',
                    'lat' => $item['lat'] ?? 0,
                    'lon' => $item['lon'] ?? 0,
                    'type' => $item['type'] ?? 'location'
                ];
            }
        }
        
        return response()->json($results);
    }

    // Auto-detect coordinates from address (AJAX endpoint)
    public function geocode(Request $request)
    {
        $address = $request->get('address');
        
        if (empty($address)) {
            return response()->json([
                'success' => false,
                'message' => 'Address is required'
            ]);
        }
        
        $coords = $this->geocodeAddress($address);
        
        if ($coords) {
            return response()->json([
                'success' => true,
                'latitude' => $coords['lat'],
                'longitude' => $coords['lng']
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Could not find coordinates for this address'
        ]);
    }
}