<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mosque;
use App\Models\MosquePhoto;
use Illuminate\Support\Facades\Storage;

class MigrateImageUrlToPhotos extends Command
{
    protected $signature = 'mosque:migrate-imageurl {--copy : copy files into storage/app/public/mosques/{id} when possible}';
    protected $description = 'Migrate legacy image_url column on mosques into mosque_photos table (creates photo records)';

    public function handle()
    {
        $this->info('Starting migration of image_url -> mosque_photos');
        $query = Mosque::whereNotNull('image_url');
        $count = $query->count();
        $this->info("Found {$count} mosques with image_url set.");
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        foreach ($query->cursor() as $m) {
            $bar->advance();
            $url = $m->image_url;
            if (!$url) continue;
            try {
                $path = null;
                // heuristics: if url contains '/storage/' assume it's already in storage
                if (strpos($url, '/storage/') !== false) {
                    $candidate = preg_replace('#.*/storage/#', '', $url);
                    if (Storage::disk('public')->exists($candidate)) {
                        $path = $candidate;
                    } elseif ($this->option('copy')) {
                        // try to copy from public path
                        $src = public_path($url);
                        if (file_exists($src)) {
                            $dest = "mosques/{$m->id}/" . basename($src);
                            Storage::disk('public')->put($dest, file_get_contents($src));
                            $path = $dest;
                        }
                    }
                } elseif (filter_var($url, FILTER_VALIDATE_URL)) {
                    // try to fetch remote image
                    if ($this->option('copy')) {
                        $contents = @file_get_contents($url);
                        if ($contents !== false) {
                            $dest = "mosques/{$m->id}/" . basename(parse_url($url, PHP_URL_PATH));
                            Storage::disk('public')->put($dest, $contents);
                            $path = $dest;
                        }
                    }
                } else {
                    // relative path, try storage path
                    $candidate = ltrim($url, '/');
                    if (Storage::disk('public')->exists($candidate)) $path = $candidate;
                }

                if ($path) {
                    // create photo record only if not existing for same path
                    $exists = MosquePhoto::where('mosque_id', $m->id)->where('path', $path)->exists();
                    if (!$exists) {
                        MosquePhoto::create(['mosque_id' => $m->id, 'path' => $path, 'caption' => 'Migrated from image_url']);
                    }
                } else {
                    // still create a record with path = image_url (as-is), but it's risky; skip to be safe
                    // log message
                    $this->line("Could not resolve file for mosque {$m->id} ({$m->name}), image_url: {$url}");
                }
            } catch (\Throwable $e) {
                $this->error('Error migrating mosque ' . $m->id . ': ' . $e->getMessage());
            }
        }
        $bar->finish();
        $this->info('\nMigration complete. Please verify photos and then consider dropping image_url column.');
        return 0;
    }
}
