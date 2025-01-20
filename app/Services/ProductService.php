<?php

namespace App\Services;

use App\Models\Product_variant;

class ProductService
{
    public function __construct()
    {
        // Constructor logic
    }
    public function updateStock($variantId, $quantity, $isIncrement = true)
    {
        $productVariant = Product_variant::findOrFail($variantId);

        if ($isIncrement) {
            $productVariant->quantity += $quantity;
        } else {
            $productVariant->quantity -= $quantity;
        }

        $productVariant->save();
        return $productVariant;
    }
}
