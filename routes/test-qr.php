<?php

use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

Route::get('/test-qr', function () {
    try {
        $data = 'Test QR Code';
        $qr = QrCode::format('png')
            ->size(200)
            ->margin(2)
            ->errorCorrection('H')
            ->color(111, 78, 55)
            ->backgroundColor(255, 255, 255)
            ->generate($data);
        
        return response($qr)->header('Content-Type', 'image/png');
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 400);
    }
});
