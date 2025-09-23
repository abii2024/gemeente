<?php

namespace App\Console\Commands;

use App\Models\Complaint;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PurgeOldComplaints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'complaints:purge 
                            {--days= : Number of days to retain complaints (overrides setting)}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge old complaints according to data retention policy (GDPR compliance)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $retentionDays = $this->option('days') ?? Setting::getRetentionDays();
        $isDryRun = $this->option('dry-run');

        $this->info("Starting complaint purge process...");
        $this->info("Retention period: {$retentionDays} days");
        if ($isDryRun) {
            $this->warn("DRY RUN MODE - No data will actually be deleted");
        }

        // Find complaints older than retention period
        $cutoffDate = now()->subDays($retentionDays);
        $oldComplaints = Complaint::where('created_at', '<', $cutoffDate)
            ->with(['attachments'])
            ->get();

        if ($oldComplaints->isEmpty()) {
            $this->info("No complaints found older than {$retentionDays} days.");
            return self::SUCCESS;
        }

        $this->info("Found {$oldComplaints->count()} complaints to purge.");

        $totalAttachments = 0;
        $totalStorage = 0;

        foreach ($oldComplaints as $complaint) {
            $this->line("Processing complaint #{$complaint->id} (created: {$complaint->created_at->format('Y-m-d')})");
            
            // Handle attachments
            foreach ($complaint->attachments as $attachment) {
                $totalAttachments++;
                $totalStorage += $attachment->size;
                
                if (!$isDryRun) {
                    // Delete physical file
                    if (Storage::disk('public')->exists($attachment->path)) {
                        Storage::disk('public')->delete($attachment->path);
                    }
                    
                    // Delete thumbnail if exists
                    $thumbnailPath = str_replace('complaints/', 'complaints/thumbnails/', $attachment->path);
                    if (Storage::disk('public')->exists($thumbnailPath)) {
                        Storage::disk('public')->delete($thumbnailPath);
                    }
                }
            }

            if (!$isDryRun) {
                // Log the purge (without PII)
                Log::info('Complaint purged for data retention compliance', [
                    'complaint_id' => $complaint->id,
                    'created_at' => $complaint->created_at,
                    'category' => $complaint->category,
                    'status' => $complaint->status,
                    'attachment_count' => $complaint->attachments->count(),
                    'retention_days' => $retentionDays
                ]);

                // Delete complaint (cascade will handle related records)
                $complaint->delete();
            }
        }

        if ($isDryRun) {
            $this->info("DRY RUN SUMMARY:");
            $this->info("- Complaints to delete: {$oldComplaints->count()}");
            $this->info("- Attachments to delete: {$totalAttachments}");
            $this->info("- Storage to free: " . $this->formatBytes($totalStorage));
        } else {
            $this->info("PURGE COMPLETED:");
            $this->info("- Deleted complaints: {$oldComplaints->count()}");
            $this->info("- Deleted attachments: {$totalAttachments}");
            $this->info("- Freed storage: " . $this->formatBytes($totalStorage));
            
            Log::info('Complaint retention purge completed', [
                'deleted_complaints' => $oldComplaints->count(),
                'deleted_attachments' => $totalAttachments,
                'freed_storage_bytes' => $totalStorage,
                'retention_days' => $retentionDays
            ]);
        }

        return self::SUCCESS;
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        
        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[$factor]);
    }
}
