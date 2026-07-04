<?php

namespace App\Helpers;

class QrHelper
{
    public static function generate($data, $size = 300, $logoPath = null)
    {
        try {
            // Simple QR generation without logo
            $qr = \QrCode::format('png')
                ->size($size)
                ->margin(2)
                ->errorCorrection('H')
                ->color(111, 78, 55)
                ->backgroundColor(255, 255, 255)
                ->generate($data);
            
            return $qr;
        } catch (\Exception $e) {
            \Log::error('QR Error: ' . $e->getMessage());
            return null;
        }
    }

    public static function getLogoPath()
    {
        return null;
    }

    public static function getDefaultQrData($customer)
    {
        return json_encode([
            'id' => $customer->id,
            'name' => $customer->name,
            'code' => $customer->customer_code
        ]);
    }
}