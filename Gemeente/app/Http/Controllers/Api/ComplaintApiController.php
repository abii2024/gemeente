<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Complaint::with(['attachments']);

        // Recent complaints
        if ($request->filled('recent')) {
            $days = (int) $request->recent;
            $query->recent($days);
        }

        $complaints = $query->orderBy('created_at', 'desc')->get();

        return ComplaintResource::collection($complaints);
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['attachments', 'notes.user', 'statusHistories.user']);

        return new ComplaintResource($complaint);
    }
}