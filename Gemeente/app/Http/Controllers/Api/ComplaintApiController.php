<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use App\Models\ComplaintNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplaintApiController extends Controller
{
    /**
     * Get all complaints with optional filters
     * GET /api/complaints?status=open&priority=high&limit=10
     */
    public function index(Request $request): JsonResponse
    {
        $query = Complaint::with(['attachments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Recent complaints (legacy support)
        if ($request->filled('recent')) {
            $days = (int) $request->recent;
            $query->recent($days);
        }

        // Limit results
        $limit = $request->filled('limit') ? (int) $request->limit : 10;

        $complaints = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => ComplaintResource::collection($complaints),
            'count' => $complaints->count(),
        ]);
    }

    /**
     * Get a specific complaint by ID
     * GET /api/complaints/{id}
     */
    public function show(Complaint $complaint): JsonResponse
    {
        $complaint->load(['attachments', 'notes.user', 'statusHistories.user']);

        return response()->json([
            'success' => true,
            'data' => new ComplaintResource($complaint),
        ]);
    }

    /**
     * Create a new complaint
     * POST /api/complaints
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:infrastructuur,afval,overlast,openbare_ruimte,verkeer,overig',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'reporter_name' => 'required|string|max:255',
            'reporter_email' => 'required|email',
            'reporter_phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $complaint = Complaint::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority ?? 'medium',
            'status' => 'open',
            'reporter_name' => $request->reporter_name,
            'reporter_email' => $request->reporter_email,
            'reporter_phone' => $request->reporter_phone,
            'location' => $request->location,
            'lat' => $request->lat,
            'lng' => $request->lng,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Klacht succesvol aangemaakt',
            'data' => new ComplaintResource($complaint),
        ], 201);
    }

    /**
     * Update complaint status
     * PATCH /api/complaints/{id}/status
     */
    public function updateStatus(Request $request, Complaint $complaint): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldStatus = $complaint->status;
        $complaint->update(['status' => $request->status]);

        // Create status history entry
        $complaint->statusHistories()->create([
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'user_id' => auth()->id(),
            'notes' => $request->notes ?? "Status gewijzigd via API",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Status bijgewerkt naar: {$request->status}",
            'data' => new ComplaintResource($complaint->fresh()),
        ]);
    }

    /**
     * Add a note to a complaint
     * POST /api/complaints/{id}/notes
     */
    public function addNote(Request $request, Complaint $complaint): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required|string',
            'body' => 'nullable|string', // Alternative field name
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $noteText = $request->note ?? $request->body;

        $note = $complaint->notes()->create([
            'body' => $noteText,
            'user_id' => auth()->id() ?? 1, // Default to admin
            'is_internal' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notitie toegevoegd',
            'data' => [
                'id' => $note->id,
                'body' => $note->body,
                'created_at' => $note->created_at,
            ],
        ], 201);
    }

    /**
     * Search complaints
     * GET /api/complaints/search?q=zoekterm&limit=10
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', $request->input('query', ''));
        $limit = $request->input('limit', 10);

        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Zoekterm is verplicht',
            ], 400);
        }

        $complaints = Complaint::where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhere('location', 'like', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'query' => $query,
            'data' => ComplaintResource::collection($complaints),
            'count' => $complaints->count(),
        ]);
    }

    /**
     * Get complaints for map display
     * GET /api/complaints/map?status=open
     */
    public function mapData(Request $request): JsonResponse
    {
        $query = Complaint::whereNotNull('lat')
            ->whereNotNull('lng');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $complaints = $query->get()->map(function ($complaint) {
            return [
                'id' => $complaint->id,
                'title' => $complaint->title,
                'status' => $complaint->status,
                'priority' => $complaint->priority,
                'category' => $complaint->category,
                'location' => $complaint->location,
                'lat' => (float) $complaint->lat,
                'lng' => (float) $complaint->lng,
                'created_at' => $complaint->created_at->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $complaints,
            'count' => $complaints->count(),
        ]);
    }
}
