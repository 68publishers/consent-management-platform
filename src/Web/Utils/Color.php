<?php

declare(strict_types=1);

namespace App\Web\Utils;

use Spatie\Color\Hex;

final class Color
{
    public static function resolveFontColor(string $hexColor): string
    {
        $hex = Hex::fromString($hexColor);
        $rgb = $hex->toRgb();

        return (0.299 * $rgb->red() + 0.587 * $rgb->green() + 0.114 * $rgb->blue()) > 128 ? '#000000' : '#ffffff';
    }
}
