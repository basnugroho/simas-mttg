<?php

namespace App\Models;

/**
 * Backwards-compatible alias: `Region` singular used in relationships and code.
 * The canonical model file is `Regions.php` (plural) in this codebase — to avoid
 * class-not-found errors we provide a thin alias that extends the existing class.
 */
class Region extends Regions
{
    // intentionally empty; inherits everything from Regions
}
