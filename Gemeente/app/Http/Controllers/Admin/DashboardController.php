<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_complaints' => Complaint::count(),
            'open_complaints' => Complaint::open()->count(),
            'in_behandeling_complaints' => Complaint::inBehandeling()->count(),
            'opgelost_complaints' => Complaint::opgelost()->count(),
        ];

        $recent_complaints = Complaint::with(['attachments'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_complaints'));
    }

    /**
     * Get recent complaints (AJAX endpoint).
     */
    public function recentComplaints(): JsonResponse
    {
        $complaints = Complaint::with(['attachments'])
            ->select('id', 'title', 'category', 'priority', 'status', 'location', 'created_at')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $complaints,
        ]);
    }

    /**
     * Search complaint by ID.
     */
    public function searchById(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:complaints,id',
        ]);

        $complaint = Complaint::with(['attachments', 'notes'])
            ->findOrFail($validated['id']);

        return response()->json([
            'success' => true,
            'data' => $complaint,
        ]);
    }

    /**
     * Get all complaints with coordinates for map display.
     */
    public function mapData(): JsonResponse
    {
        $complaints = Complaint::select(
            'id',
            'title',
            'description',
            'category',
            'priority',
            'status',
            'location',
            'lat',
            'lng',
            'created_at'
        )
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get()
            ->map(function ($complaint) {
                return [
                    'id' => $complaint->id,
                    'title' => $complaint->title,
                    'description' => \Illuminate\Support\Str::limit($complaint->description, 100),
                    'category' => $complaint->category,
                    'priority' => $complaint->priority,
                    'status' => $complaint->status,
                    'location' => $complaint->location,
                    'lat' => (float) $complaint->lat,
                    'lng' => (float) $complaint->lng,
                    'created_at' => $complaint->created_at->format('d-m-Y H:i'),
                    'statusColor' => $this->getStatusColor($complaint->status),
                    'priorityColor' => $this->getPriorityColor($complaint->priority),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $complaints,
        ]);
    }

    /**
     * Get complaint details for map popup.
     */
    public function complaintDetails(int $id): JsonResponse
    {
        $complaint = Complaint::with(['attachments'])
            ->select(
                'id',
                'title',
                'description',
                'category',
                'priority',
                'status',
                'location',
                'reporter_name',
                'created_at',
                'updated_at'
            )
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $complaint->id,
                'title' => $complaint->title,
                'description' => $complaint->description,
                'category' => $complaint->category,
                'priority' => $complaint->priority,
                'status' => $complaint->status,
                'location' => $complaint->location,
                'reporter_name' => $complaint->reporter_name,
                'attachments_count' => $complaint->attachments->count(),
                'created_at' => $complaint->created_at->format('d-m-Y H:i'),
                'url' => route('admin.complaints.show', $complaint->id),
            ],
        ]);
    }

    /**
     * Filter complaints based on criteria.
     */
    public function filter(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'nullable|string|in:open,in_progress,resolved,closed',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'category' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = Complaint::query()
            ->select('id', 'title', 'category', 'priority', 'status', 'location', 'lat', 'lng', 'created_at')
            ->whereNotNull('lat')
            ->whereNotNull('lng');

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['priority'])) {
            $query->where('priority', $validated['priority']);
        }

        if (!empty($validated['category'])) {
            $query->where('category', $validated['category']);
        }

        if (!empty($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }

        $complaints = $query->get()->map(function ($complaint) {
            return [
                'id' => $complaint->id,
                'title' => $complaint->title,
                'category' => $complaint->category,
                'priority' => $complaint->priority,
                'status' => $complaint->status,
                'location' => $complaint->location,
                'lat' => (float) $complaint->lat,
                'lng' => (float) $complaint->lng,
                'created_at' => $complaint->created_at->format('d-m-Y H:i'),
                'statusColor' => $this->getStatusColor($complaint->status),
                'priorityColor' => $this->getPriorityColor($complaint->priority),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $complaints,
        ]);
    }

    /**
     * Get color for status badge.
     */
    private function getStatusColor(string $status): string
    {
        return match ($status) {
            'open' => 'blue',
            'in_progress' => 'yellow',
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get color for priority badge.
     */
    private function getPriorityColor(string $priority): string
    {
        return match ($priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray',
        };
    }
}
