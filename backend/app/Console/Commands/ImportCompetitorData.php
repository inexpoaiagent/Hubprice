<?php

namespace App\Console\Commands;

use App\Models\CompetitorSource;
use App\Services\CompetitorCrawlerService;
use Illuminate\Console\Command;

class ImportCompetitorData extends Command
{
    protected $signature = 'competitors:import {--source= : Import only this source name}';
    protected $description = 'Import competitor listing data for AI market analysis';

    public function handle(CompetitorCrawlerService $crawler): int
    {
        $sourceName = $this->option('source');

        if ($sourceName) {
            $source = CompetitorSource::where('name', 'like', "%{$sourceName}%")->first();
            if (!$source) { $this->error("Source not found: {$sourceName}"); return 1; }
            $result = $crawler->crawlSource($source);
            $this->table(['Source', 'Imported', 'Updated', 'Errors'], [
                [$result['source'], $result['imported'], $result['updated'], $result['errors']],
            ]);
        } else {
            $sources = CompetitorSource::where('is_active', true)->get();
            if ($sources->isEmpty()) { $this->warn('No active competitor sources found.'); return 0; }
            $this->info("Importing from {$sources->count()} source(s)...");
            $rows = [];
            foreach ($sources as $source) {
                $result = $crawler->crawlSource($source);
                $rows[] = [$result['source'], $result['imported'], $result['updated'], $result['errors']];
                $this->line("  done {$result['source']}: +{$result['imported']} new, {$result['updated']} updated");
            }
            $this->table(['Source', 'Imported', 'Updated', 'Errors'], $rows);
        }

        $this->info('Import complete.');
        return 0;
    }
}