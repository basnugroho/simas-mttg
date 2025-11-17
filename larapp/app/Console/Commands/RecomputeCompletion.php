<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mosque;

class RecomputeCompletion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosques:recompute-completion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recompute completion_percentage for all mosques based on required facilities.';

    public function handle()
    {
        $this->info('Recomputing completion percentage for mosques...');
        $bar = $this->output->createProgressBar(Mosque::count());
        $bar->start();
        foreach (Mosque::cursor() as $m) {
            try {
                $m->recomputeCompletionPercentage();
            } catch (\Throwable $e) {
                $this->warn("Failed for mosque {$m->id}: " . $e->getMessage());
            }
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
        $this->info('Done.');
        return 0;
    }
}
