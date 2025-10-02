<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanTempUploads extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'uploads:clean-temp
                            {--hours=24 : Number of hours to retain temporary uploads}
                            {--dry-run : Show what would be deleted without removing files}';

    /**
     * The console command description.
     */
    protected $description = 'Verwijder tijdelijke upload-bestanden ouder dan de ingestelde bewaartermijn.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $hours = $hours > 0 ? $hours : 24;
        $dryRun = (bool) $this->option('dry-run');

        $cutoffTimestamp = now()->subHours($hours)->getTimestamp();
        $disk = Storage::disk('public');
        $directories = ['temp-uploads', 'temp-uploads/thumbnails'];

        $deletedFiles = 0;
        $freedBytes = 0;

        $this->info("Cleaning temporary uploads older than {$hours} hours...");
        if ($dryRun) {
            $this->warn('Running in dry-run mode. No files will be deleted.');
        }

        foreach ($directories as $directory) {
            if (! $disk->exists($directory)) {
                continue;
            }

            foreach ($disk->allFiles($directory) as $path) {
                $lastModified = $disk->lastModified($path);

                if ($lastModified === false || (int) $lastModified >= $cutoffTimestamp) {
                    continue;
                }

                $size = $disk->size($path) ?: 0;

                if ($dryRun) {
                    $this->line("[DRY RUN] {$path} (".$this->formatBytes($size).')');

                    continue;
                }

                if ($disk->delete($path)) {
                    $deletedFiles++;
                    $freedBytes += $size;
                    $this->line("Verwijderd: {$path}");
                }
            }
        }

        if ($dryRun) {
            $this->info('Dry-run afgerond.');
        } else {
            $message = "Verwijderd {$deletedFiles} bestanden en ".$this->formatBytes($freedBytes).' vrijgemaakt.';
            $this->info($message);
            Log::info('Temp uploads opgeschoond', [
                'deleted_files' => $deletedFiles,
                'freed_bytes' => $freedBytes,
                'retention_hours' => $hours,
            ]);
        }

        return self::SUCCESS;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        return sprintf('%.2f %s', $bytes / (1024 ** $power), $units[$power]);
    }
}
