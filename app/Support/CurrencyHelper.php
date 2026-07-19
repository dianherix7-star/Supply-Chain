<?php

namespace App\Support;

class CurrencyHelper
{
    /**
     * Ambil kode mata uang pertama dari string (misal: "IDR, USD" → "IDR").
     */
    public static function primaryCode(?string $currency): ?string
    {
        if (empty($currency)) {
            return null;
        }

        $parts = preg_split('/\s*,\s*/', trim($currency));

        return !empty($parts[0]) ? strtoupper(trim($parts[0])) : null;
    }
}
