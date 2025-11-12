<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\StatusHistory;
use App\Services\PrivacyLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintAdminController extends Controller
{
    public function index(Request $request): View
    {
        $query = Complaint::with(['attachments']);

        // Search by ID
        if ($request->filled('search')) {
            $query->where('id', $request->search)
                ->orWhere('title', 'like', '%'.$request->search.'%')
                ->orWhere('reporter_name', 'like', '%'.$request->search.'%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $complaints = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint): View
    {
        $complaint->load(['attachments', 'notes.user', 'statusHistories.user']);

        return view('admin.complaints.show', compact('complaint'));
    }

    public function edit(Complaint $complaint): View
    {
        return view('admin.complaints.edit', compact('complaint'));
    }

    public function update(Request $request, Complaint $complaint): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|in:wegen_onderhoud,stoepen,fietspaden,verkeersborden,wegmarkeringen,parkeren,straatverlichting,parkverlichting,tunnel_verlichting,afval_ophaling,afval_container,zwerfvuil,graffiti,hondenpoep,straat_reiniging,bomen,onkruid,parken,speeltuinen,groenstroken,riool_verstopt,wateroverlast,put_deksel,lekkage,straatmeubilair,fietsenrekken,bushokje,speelplaats,bankjes,geluidsoverlast,stankoverlast,wildgroei,drugsoverlast,dierenplaag,illegaal_afval,overig',
            'status' => 'nullable|in:open,in_progress,resolved,closed',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'location' => 'nullable|string|max:500',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'reporter_name' => 'required|string|max:255',
            'reporter_email' => 'required|email|max:255',
            'reporter_phone' => 'nullable|string|max:255',
            'internal_notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        // Track status change if status was updated
        if (isset($validated['status']) && $validated['status'] !== $complaint->status) {
            $oldStatus = $complaint->status;
            $newStatus = $validated['status'];

            // Record status change
            StatusHistory::create([
                'complaint_id' => $complaint->id,
                'user_id' => auth()->id(),
                'from' => $oldStatus,
                'to' => $newStatus,
            ]);

            // Log status change without PII
            PrivacyLogger::logComplaintAction('status_changed', $complaint->id, [
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'admin_user_id' => auth()->id(),
            ]);
        }

        $complaint->update($validated);

        return redirect()->route('admin.complaints.edit', $complaint)
            ->with('success', 'Klacht succesvol bijgewerkt.');
    }

    public function updateStatus(Request $request, Complaint $complaint): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:open,in_behandeling,opgelost',
        ]);

        $oldStatus = $complaint->status;
        $newStatus = $request->status;

        if ($oldStatus !== $newStatus) {
            // Update complaint status
            $complaint->update(['status' => $newStatus]);

            // Record status change
            StatusHistory::create([
                'complaint_id' => $complaint->id,
                'user_id' => auth()->id(),
                'from' => $oldStatus,
                'to' => $newStatus,
            ]);

            // Log status change without PII
            PrivacyLogger::logComplaintAction('status_changed', $complaint->id, [
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'admin_user_id' => auth()->id(),
            ]);
        }

        return redirect()->back()->with('success', 'Status bijgewerkt naar: '.$newStatus);
    }

    public function destroy(Complaint $complaint): RedirectResponse
    {
        $complaintId = $complaint->id;
        $complaint->delete();

        // Log complaint deletion
        PrivacyLogger::logComplaintAction('deleted', $complaintId, [
            'admin_user_id' => auth()->id(),
            'deletion_reason' => 'admin_action',
        ]);

        return redirect()->route('admin.complaints.index')
            ->with('success', 'Klacht succesvol verwijderd.');
    }

    public function map(): View
    {
        $complaints = Complaint::whereNotNull('lat')
            ->whereNotNull('lng')
            ->with(['attachments'])
            ->get();

        return view('admin.complaints.map', compact('complaints'));
    }
}
