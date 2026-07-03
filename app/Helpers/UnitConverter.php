<?php

namespace App\Helpers;

use App\Models\Item;

class UnitConverter
{
    // Conversion factors to base unit (grams for mass, ml for volume)
    private static $conversions = [
        // Mass units (to grams)
        'g' => 1,
        'kg' => 1000,
        'mg' => 0.001,
        'lb' => 453.592,
        'oz' => 28.3495,
        
        // Volume units (to milliliters)
        'ml' => 1,
        'liters' => 1000,
        'tbsp' => 15,
        'tsp' => 5,
        'cup' => 240,
        'fl oz' => 29.5735,
        
        // Piece units
        'pieces' => 1,
        'packs' => 1,
        'bottles' => 1,
        'bags' => 1,
        'cans' => 1,
        'boxes' => 1,
        'trays' => 1,
        'sacks' => 1,
    ];

    // Density for converting volume to mass (g/ml)
    private static $densities = [
        'coffee' => 0.6,
        'milk' => 1.03,
        'sugar' => 0.85,
        'flour' => 0.59,
        'water' => 1.0,
        'oil' => 0.92,
        'vanilla' => 0.95,
        'chocolate' => 1.2,
        'cream' => 1.0,
        'syrup' => 1.33,
        'honey' => 1.42,
        'butter' => 0.91,
        'salt' => 1.2,
        'cocoa' => 0.5,
        'matcha' => 0.5,
        'yeast' => 0.6,
    ];

    public static function convert($value, $fromUnit, $toUnit, $itemName = null, $itemId = null)
    {
        // If same unit, return as is
        if ($fromUnit === $toUnit) {
            return $value;
        }

        // Check if item has weight_per_unit (for bags, sacks, packs, etc.)
        if ($itemId) {
            $item = Item::find($itemId);
            if ($item && $item->weight_per_unit) {
                $weightPerUnit = $item->weight_per_unit; // e.g., 50000g per bag
                
                // CONVERTING FROM BAGS TO GRAMS: value (bags) * weight_per_unit (g per bag)
                if (in_array($fromUnit, ['bags', 'sacks', 'packs', 'bottles', 'cans', 'boxes', 'trays']) && $toUnit === 'g') {
                    return $value * $weightPerUnit;
                }
                // CONVERTING FROM GRAMS TO BAGS: value (g) / weight_per_unit (g per bag)
                if ($fromUnit === 'g' && in_array($toUnit, ['bags', 'sacks', 'packs', 'bottles', 'cans', 'boxes', 'trays'])) {
                    return $value / $weightPerUnit;
                }
                // CONVERTING FROM BAGS TO KG: value (bags) * weight_per_unit / 1000
                if (in_array($fromUnit, ['bags', 'sacks', 'packs', 'bottles', 'cans', 'boxes', 'trays']) && $toUnit === 'kg') {
                    return ($value * $weightPerUnit) / 1000;
                }
                // CONVERTING FROM KG TO BAGS: value (kg) * 1000 / weight_per_unit
                if ($fromUnit === 'kg' && in_array($toUnit, ['bags', 'sacks', 'packs', 'bottles', 'cans', 'boxes', 'trays'])) {
                    return ($value * 1000) / $weightPerUnit;
                }
                // CONVERTING FROM BAGS TO ML
                if (in_array($fromUnit, ['bags', 'sacks', 'packs', 'bottles', 'cans', 'boxes', 'trays']) && $toUnit === 'ml') {
                    return $value * ($item->volume_per_unit ?? 1000);
                }
                // CONVERTING FROM ML TO BAGS
                if ($fromUnit === 'ml' && in_array($toUnit, ['bags', 'sacks', 'packs', 'bottles', 'cans', 'boxes', 'trays'])) {
                    return $value / ($item->volume_per_unit ?? 1000);
                }
                // CONVERTING FROM BAGS TO PIECES (for trays)
                if (in_array($fromUnit, ['bags', 'sacks', 'packs', 'bottles', 'cans', 'boxes', 'trays']) && $toUnit === 'pieces') {
                    return $value * $weightPerUnit;
                }
                // CONVERTING FROM PIECES TO BAGS (for trays)
                if ($fromUnit === 'pieces' && in_array($toUnit, ['bags', 'sacks', 'packs', 'bottles', 'cans', 'boxes', 'trays'])) {
                    return $value / $weightPerUnit;
                }
            }
        }

        // Standard conversions (kg to g, g to kg, liters to ml, ml to liters)
        // kg to g
        if ($fromUnit === 'kg' && $toUnit === 'g') {
            return $value * 1000;
        }
        // g to kg
        if ($fromUnit === 'g' && $toUnit === 'kg') {
            return $value * 0.001;
        }
        // liters to ml
        if ($fromUnit === 'liters' && $toUnit === 'ml') {
            return $value * 1000;
        }
        // ml to liters
        if ($fromUnit === 'ml' && $toUnit === 'liters') {
            return $value * 0.001;
        }
        // tbsp to g (approx)
        if ($fromUnit === 'tbsp' && $toUnit === 'g') {
            return $value * 15;
        }
        // tsp to g (approx)
        if ($fromUnit === 'tsp' && $toUnit === 'g') {
            return $value * 5;
        }
        // g to tbsp (approx)
        if ($fromUnit === 'g' && $toUnit === 'tbsp') {
            return $value / 15;
        }
        // g to tsp (approx)
        if ($fromUnit === 'g' && $toUnit === 'tsp') {
            return $value / 5;
        }

        // If no specific conversion found, return as is
        return $value;
    }

    public static function getDensity($itemName)
    {
        if (!$itemName) {
            return 1.0;
        }
        
        foreach (self::$densities as $key => $density) {
            if (strpos(strtolower($itemName), $key) !== false) {
                return $density;
            }
        }
        
        return 1.0;
    }

    public static function getUnitType($unit)
    {
        $unit = strtolower(trim($unit));
        
        $massUnits = ['g', 'kg', 'mg', 'lb', 'oz'];
        $volumeUnits = ['ml', 'liters', 'tbsp', 'tsp', 'cup', 'fl oz'];
        $containerUnits = ['bags', 'sacks', 'packs', 'bottles', 'cans', 'boxes', 'trays'];
        $pieceUnits = ['pieces'];
        
        if (in_array($unit, $massUnits)) return 'mass';
        if (in_array($unit, $volumeUnits)) return 'volume';
        if (in_array($unit, $containerUnits)) return 'container';
        if (in_array($unit, $pieceUnits)) return 'pieces';
        
        return 'unknown';
    }

    public static function getConversionInfo($fromUnit, $toUnit, $itemName = null, $itemId = null)
    {
        $converted = self::convert(1, $fromUnit, $toUnit, $itemName, $itemId);
        return [
            'from' => $fromUnit,
            'to' => $toUnit,
            'factor' => $converted,
            'display' => "1 $fromUnit = " . number_format($converted, 4) . " $toUnit"
        ];
    }
}