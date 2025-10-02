<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchApiController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'nullable|integer',
            'status' => 'nullable|in:open,in_behandeling,opgelost',
            'q' => 'nullable|string|min:3',
        ]);

        $query = Complaint::with(['attachments']);

        // Search by ID
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        // Search by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Text search
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%'.$searchTerm.'%')
                    ->orWhere('description', 'like', '%'.$searchTerm.'%')
                    ->orWhere('reporter_name', 'like', '%'.$searchTerm.'%');
            });
        }

        $complaints = $query->orderBy('created_at', 'desc')->limit(50)->get();

        return ComplaintResource::collection($complaints);
    }
}
