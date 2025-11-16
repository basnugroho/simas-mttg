<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Regions extends Model
{
    protected $fillable = [
        'name', 'type', 'type_key', 'pov', 'pov_mappings', 'code', 'parent_id', 'level',
    ];

    protected $casts = [
        'pov_mappings' => 'array',
    ];

    // Central list of allowed region types (used by forms/UI)
    public const TYPES = [
        'AREA' => 'Area',
        'STO' => 'STO',
        'WITEL_OLD' => 'WITEL (Old)',
        'SUB_DISTRICT' => 'Sub District',
        'DISTRICT' => 'District',
        'WITEL' => 'Witel',
        'TERRITORY' => 'Territory',
        'TREG' => 'TREG',
        'TREG_OLD' => 'TREG (Old)',
        'OTHER' => 'Other',
    ];

    // Preferred UI ordering for types from largest to smallest (used when no POV-specific order)
    public const TYPE_UI_ORDER = [
        'AREA',       // Regional / Area
        'TERRITORY',  // Pulau / Territory
        'TREG',
        'TREG_OLD',
        'WITEL',
        'STO',
        'DISTRICT',   // Kabupaten/Kota
        'SUB_DISTRICT', // Kecamatan / Kelurahan
        'OTHER',
    ];

    // Points of View supported for editing/representation
    public const POVS = [
        'ALL' => 'All (default)',
        'TELKOM_OLD' => 'Telkom (Old)',
        'TELKOM_NEW' => 'Telkom (New)',
        'TIF' => 'TIF',
    ];

    // Canonical level values used across the app and UI
    public const LEVELS = [
        'REGIONAL',
        'AREA',
        'WITEL',
        'STO',
        'OTHER',
    ];

    /**
     * Return type options, optionally scoped to a POV.
     * If no POV provided, return the canonical list.
     */
    public static function typeOptions(?string $pov = null)
    {
        if (! $pov) {
            return self::TYPES;
        }

        // Example mappings per POV. These can be adjusted later.
        $map = [
            'TELKOM_OLD' => [
                'AREA' => 'Area',
                'WITEL_OLD' => 'WITEL (Old)',
                'STO' => 'STO',
                'OTHER' => 'Other',
            ],
            'TELKOM_NEW' => [
                'AREA' => 'Area',
                'WITEL' => 'Witel',
                'STO' => 'STO',
                'OTHER' => 'Other',
            ],
            'TIF' => [
                'AREA' => 'Area',
                'TERRITORY' => 'Territory',
                'DISTRICT' => 'District',
                'WITEL' => 'Witel',
                'STO' => 'STO',
                'OTHER' => 'Other',
            ],
        ];

        return $map[$pov] ?? self::TYPES;
    }

    /**
     * Default ordering priority for types for UI listing per POV.
     * Lower index = higher priority in ordering (displayed earlier).
     */
    public static function typeOrderForPov(?string $pov = null): array
    {
        $orders = [
            'TELKOM_OLD' => ['AREA', 'WITEL_OLD', 'STO', 'DISTRICT', 'OTHER'],
            'TELKOM_NEW' => ['AREA', 'WITEL', 'STO', 'DISTRICT', 'OTHER'],
            'TIF' => ['AREA', 'TERRITORY', 'DISTRICT', 'WITEL', 'STO', 'OTHER'],
        ];

        return $orders[$pov] ?? self::TYPE_UI_ORDER;
    }

    // Mapping legacy DB enum values (if still present) to canonical type keys
    public const LEGACY_MAP = [
        'PROVINCE' => 'AREA',
        'CITY' => 'STO',
        'WITEL' => 'WITEL_OLD',
    ];

    public static function legacyToTypeKey(?string $legacy)
    {
        if (! $legacy) return null;
        $legacy = strtoupper($legacy);
        return self::LEGACY_MAP[$legacy] ?? null;
    }

    /**
     * Map a canonical type_key back to legacy DB enum value when possible.
     * Returns null if there is no direct legacy equivalent (so we won't write invalid enum values).
     */
    public static function typeKeyToLegacy(?string $typeKey)
    {
        if (! $typeKey) return null;
        $rev = array_flip(self::LEGACY_MAP);
        return $rev[$typeKey] ?? null;
    }

    /**
     * Return a human-friendly label for this region's type.
     * Prefer the new `type_key`, fall back to legacy `type` mapped via LEGACY_MAP.
     */
    public function displayTypeLabel(): string
    {
        $key = $this->type_key ?? self::legacyToTypeKey($this->type ?? null);
        if ($key && isset(self::TYPES[$key])) {
            return self::TYPES[$key];
        }
        return (string)($this->type ?? '');
    }

    /**
     * Centralize safety for creating/updating Regions so legacy enum `type` isn't written
     * with unknown values and POV gets a safe default.
     */
    protected static function booted()
    {
        static::saving(function (Regions $region) {
            // Ensure pov defaults: AREA -> ALL, otherwise TELKOM_OLD
            if (empty($region->pov)) {
                $isArea = ($region->type_key === 'AREA') || (!empty($region->type) && strtoupper($region->type) === 'AREA');
                $region->pov = $isArea ? 'ALL' : 'TELKOM_OLD';
            }

            // Map canonical type_key back to legacy enum when possible; otherwise clear to avoid DB enum errors
            if (!empty($region->type_key)) {
                $legacy = self::typeKeyToLegacy($region->type_key);
                if ($legacy) {
                    $region->type = $legacy;
                } else {
                    $region->type = null;
                }
            } else {
                if (!empty($region->type) && !in_array(strtoupper($region->type), array_keys(self::LEGACY_MAP))) {
                    $region->type = null;
                }
            }
        });
    }

    /**
     * Return allowed parent type keys for a given child type_key.
     * Used to constrain parent selectors in the UI so users pick exactly one level above.
     */
    public static function allowedParentTypeKeysFor(?string $typeKey): array
    {
        // Simplified parent mapping for Telkom Old POV (and global default for now)
        $map = [
            'STO' => ['WITEL_OLD'],
            'WITEL_OLD' => ['AREA'],
            'AREA' => ['TREG_OLD'],
            // Keep other types as having no direct parent by default
            'SUB_DISTRICT' => [],
            'DISTRICT' => [],
            'WITEL' => [],
            'TERRITORY' => [],
            'TREG' => [],
            'TREG_OLD' => [],
            'OTHER' => [],
        ];

        return $map[$typeKey] ?? [];
    }

    /**
     * Given a region LEVEL, return the LEVEL of its parent (one level up in hierarchy).
     * Returns null when there is no parent (e.g., REGIONAL or unknown values).
     */
    public static function parentLevelFor(?string $level): ?string
    {
        if (! $level) return null;
        $level = strtoupper($level);
        $map = [
            'AREA' => 'REGIONAL',
            'WITEL' => 'AREA',
            'STO' => 'WITEL',
            // REGIONAL and OTHER have no parent
            'REGIONAL' => null,
            'OTHER' => null,
        ];
        return $map[$level] ?? null;
    }

    public function parent()
    {
        return $this->belongsTo(Regions::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Regions::class, 'parent_id');
    }

    public function mosques()
    {
        return $this->hasMany(Mosque::class, 'province_id'); // atau city_id/witel_id tergantung kebutuhan
    }

    /**
     * Collect descendant region ids (including the provided id) using a recursive CTE.
     * Returns array of ids. If the DB does not support recursive CTE, returns [$id].
     */
    public static function collectDescendantIds(int $id): array
    {
        try {
            $sql = "WITH RECURSIVE d AS (
                SELECT id, parent_id FROM regions WHERE id = :id
                UNION ALL
                SELECT r.id, r.parent_id FROM regions r JOIN d ON r.parent_id = d.id
            ) SELECT id FROM d";
            $rows = DB::select($sql, ['id' => $id]);
            return array_map(function($r){ return (int)$r->id; }, $rows);
        } catch (\Exception $e) {
            // Fallback: return only the id
            return [$id];
        }
    }
}
