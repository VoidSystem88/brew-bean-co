<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class QrSettingsController extends Controller
{
    public function index()
    {
        $logoExists = Storage::disk('public')->exists('qr-logo.png');
        $logoPath = $logoExists ? asset('storage/qr-logo.png') : null;
        
        return view('admin.qr-settings', compact('logoExists', 'logoPath'));
    }

    public function uploadLogo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,gif,webp|max:5120',
            'crop_x' => 'nullable|numeric',
            'crop_y' => 'nullable|numeric',
            'crop_width' => 'nullable|numeric',
            'crop_height' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $image = $request->file('logo');
            
            $cropX = $request->crop_x ?? 0;
            $cropY = $request->crop_y ?? 0;
            $cropWidth = $request->crop_width ?? null;
            $cropHeight = $request->crop_height ?? null;

            $manager = new ImageManager(new Driver());
            $img = $manager->read($image->getPathname());

            if ($cropWidth && $cropHeight) {
                $img->crop((int)$cropWidth, (int)$cropHeight, (int)$cropX, (int)$cropY);
            }

            $img->resize(200, 200);

            $path = storage_path('app/public/qr-logo.png');
            $img->save($path);

            return response()->json([
                'success' => true,
                'message' => 'Logo uploaded successfully!',
                'path' => asset('storage/qr-logo.png')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function removeLogo()
    {
        try {
            if (Storage::disk('public')->exists('qr-logo.png')) {
                Storage::disk('public')->delete('qr-logo.png');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Logo removed successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function preview(Request $request)
    {
        try {
            $data = 'Sample QR Code';
            $size = 250;
            
            $qr = \App\Helpers\QrHelper::generate($data, $size, null);
            
            if ($qr) {
                return response($qr)
                    ->header('Content-Type', 'image/png')
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            } else {
                // Generate a simple error image
                $img = imagecreate(250, 250);
                $bg = imagecolorallocate($img, 255, 255, 255);
                $textColor = imagecolorallocate($img, 200, 200, 200);
                imagestring($img, 5, 60, 115, 'QR Generator Error', $textColor);
                ob_start();
                imagepng($img);
                $png = ob_get_clean();
                imagedestroy($img);
                return response($png)->header('Content-Type', 'image/png');
            }

        } catch (\Exception $e) {
            \Log::error('QR Preview Error: ' . $e->getMessage());
            
            // Return simple error image
            $img = imagecreate(250, 250);
            $bg = imagecolorallocate($img, 255, 255, 255);
            $textColor = imagecolorallocate($img, 255, 0, 0);
            imagestring($img, 5, 50, 115, 'QR Error', $textColor);
            ob_start();
            imagepng($img);
            $png = ob_get_clean();
            imagedestroy($img);
            return response($png)->header('Content-Type', 'image/png');
        }
    }

    public function download(Request $request)
    {
        try {
            $data = 'Sample QR Code';
            $size = 300;
            
            $qr = \App\Helpers\QrHelper::generate($data, $size, null);
            
            if ($qr) {
                return response($qr)
                    ->header('Content-Type', 'image/png')
                    ->header('Content-Disposition', 'attachment; filename="qr-code.png"');
            } else {
                throw new \Exception('Failed to generate QR code');
            }

        } catch (\Exception $e) {
            \Log::error('QR Download Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }
}