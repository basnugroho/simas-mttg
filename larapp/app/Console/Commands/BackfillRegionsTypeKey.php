<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Regions;
use Illuminate\Support\Facades\DB;

class BackfillRegionsTypeKey extends Command
{
    protected $signature = 'regions:backfill-typekey {--default-pov=TELKOM_OLD} {--dry-run}';
    protected $description = 'Backfill regions.type_key and pov from legacy type values (PROVINCE, CITY, WITEL, OTHER)';

    public function handle()
    {
        $defaultPov = $this->option('default-pov') ?: 'TELKOM_OLD';
        $dry = $this->option('dry-run');

        $this->info("Starting backfill (default pov: {$defaultPov})" . ($dry ? ' (dry-run)' : ''));

        $regions = Regions::all();
        $count = 0;
        foreach ($regions as $r) {
            $oldType = strtoupper($r->type ?? '');
            $newTypeKey = null;
            $setPov = null;

            switch ($oldType) {
                case 'PROVINCE':
                    $newTypeKey = 'AREA';
                    $setPov = 'ALL';
                    break;
                case 'WITEL':
                    $newTypeKey = 'WITEL_OLD';
                    $setPov = $defaultPov;
                    break;
                case 'CITY':
                    // assume city corresponds to STO in legacy mapping
                    $newTypeKey = 'STO';
                    $setPov = $defaultPov;
                    break;
                default:
                    $newTypeKey = 'OTHER';
                    $setPov = $defaultPov;
            }

            $changes = [];
            if ($r->type_key !== $newTypeKey) {
                $changes['type_key'] = $newTypeKey;
            }
            if ($r->pov !== $setPov) {
                $changes['pov'] = $setPov;
            }

            if (!empty($changes)) {
                $count++;
                $this->line("Region {$r->id} ({$r->name}): will set type_key={$newTypeKey}, pov={$setPov}");
                if (! $dry) {
                    $r->update($changes);
                }
            }
        }

        $this->info("Processed {$regions->count()} regions, updated {$count} rows" . ($dry ? ' (dry-run, no DB changes)' : ''));
        return 0;
    }
}
