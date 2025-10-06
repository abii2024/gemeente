<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    /**
     * Get complaint statistics
     * GET /api/statistics?period=month
     */
    public function index(Request $request): JsonResponse
    {
        $period = $request->input('period', 'all');

        // Determine date range based on period
        $query = Complaint::query();

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            case 'all':
            default:
                // No date filter
                break;
        }

        // Total complaints
        $total = $query->count();

        // By status
        $byStatus = [
            'open' => (clone $query)->where('status', 'open')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'resolved' => (clone $query)->where('status', 'resolved')->count(),
            'closed' => (clone $query)->where('status', 'closed')->count(),
        ];

        // By priority
        $byPriority = [
            'low' => (clone $query)->where('priority', 'low')->count(),
            'medium' => (clone $query)->where('priority', 'medium')->count(),
            'high' => (clone $query)->where('priority', 'high')->count(),
            'urgent' => (clone $query)->where('priority', 'urgent')->count(),
        ];

        // By category
        $byCategory = [
            'infrastructuur' => (clone $query)->where('category', 'infrastructuur')->count(),
            'afval' => (clone $query)->where('category', 'afval')->count(),
            'overlast' => (clone $query)->where('category', 'overlast')->count(),
            'openbare_ruimte' => (clone $query)->where('category', 'openbare_ruimte')->count(),
            'verkeer' => (clone $query)->where('category', 'verkeer')->count(),
            'overig' => (clone $query)->where('category', 'overig')->count(),
        ];

        // Average resolution time (for resolved complaints)
        $resolvedComplaints = (clone $query)->where('status', 'resolved')->get();
        $avgResolutionTime = null;

        if ($resolvedComplaints->isNotEmpty()) {
            $totalHours = 0;
            foreach ($resolvedComplaints as $complaint) {
                $resolved = $complaint->updated_at;
                $created = $complaint->created_at;
                $totalHours += $created->diffInHours($resolved);
            }
            $avgResolutionTime = round($totalHours / $resolvedComplaints->count(), 1);
        }

        // Recent trends (last 7 days)
        $trends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Complaint::whereDate('created_at', $date)->count();
            $trends[] = [
                'date' => $date->format('Y-m-d'),
                'count' => $count,
            ];
        }

        // Most active categories
        $topCategories = Complaint::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category,
                    'count' => $item->count,
                ];
            });

        return response()->json([
            'success' => true,
            'period' => $period,
            'data' => [
                'total' => $total,
                'by_status' => $byStatus,
                'by_priority' => $byPriority,
                'by_category' => $byCategory,
                'avg_resolution_time_hours' => $avgResolutionTime,
                'trends_last_7_days' => $trends,
                'top_categories' => $topCategories,
            ],
        ]);
    }
}
