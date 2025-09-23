<?php

namespace App\Console\Commands;

use App\Models\Complaint;
use App\Services\PrivacyLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOverdueComplaints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'complaints:check-overdue {--days=14 : Number of days to consider overdue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for complaints older than specified days that are not resolved';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        
        $this->info("Checking for complaints older than {$days} days that are not resolved...");
        
        $overdueComplaints = Complaint::where('created_at', '<', now()->subDays($days))
            ->whereIn('status', ['open', 'in_behandeling'])
            ->get();
            
        if ($overdueComplaints->isEmpty()) {
            $this->info('No overdue complaints found.');
            PrivacyLogger::logAudit('overdue_check_completed', [
                'overdue_count' => 0,
                'days_threshold' => $days,
            ]);
            return self::SUCCESS;
        }
        
        $count = $overdueComplaints->count();
        $this->warn("Found {$count} overdue complaints:");
        
        foreach ($overdueComplaints as $complaint) {
            $daysOld = $complaint->created_at->diffInDays(now());
            $this->line("- Complaint #{$complaint->id}: {$complaint->title} (created {$daysOld} days ago, status: {$complaint->status})");
            
            // Log each overdue complaint for admin attention
            PrivacyLogger::logAudit('overdue_complaint_detected', [
                'complaint_id' => $complaint->id,
                'days_old' => $daysOld,
                'current_status' => $complaint->status,
                'category' => $complaint->category,
            ]);
        }
        
        // Send notification to admins (in real implementation)
        $this->comment("Notifications would be sent to administrators about {$count} overdue complaints.");
        
        PrivacyLogger::logAudit('overdue_check_completed', [
            'overdue_count' => $count,
            'days_threshold' => $days,
        ]);
        
        return self::SUCCESS;
    }
}
