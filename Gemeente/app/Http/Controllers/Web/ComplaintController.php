<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Models\Complaint;
use App\Services\PrivacyLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function create(): View
    {
        return view('pages.complaint-create');
    }

    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        $complaint = Complaint::create($request->validated());

        // Log complaint creation without PII
        PrivacyLogger::logComplaintAction('created', $complaint->id, [
            'category' => $complaint->category,
            'priority' => $complaint->priority,
            'has_coordinates' => $complaint->lat !== null && $complaint->lng !== null,
            'has_location_description' => filled($complaint->location),
            'attachment_count' => 0,
        ]);

        // Handle file uploads
        $attachmentCount = 0;
        if ($request->hasFile('attachments')) {
            $imageManager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver);

            foreach ($request->file('attachments') as $file) {
                $originalPath = $file->store('complaints/originals', 'public');

                // Resize image if it's an image file
                $finalPath = $originalPath;
                if (in_array($file->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])) {
                    $image = $imageManager->read($file);

                    // Resize to max 1920x1080 while maintaining aspect ratio
                    $image->scaleDown(width: 1920, height: 1080);

                    // Save resized version
                    $resizedPath = 'complaints/'.basename($originalPath);
                    $absolutePath = Storage::disk('public')->path($resizedPath);

                    if (! is_dir(dirname($absolutePath))) {
                        mkdir(dirname($absolutePath), 0755, true);
                    }

                    $image->save($absolutePath, quality: 85);
                    $finalPath = $resizedPath;

                    // Remove original if resizing was successful
                    Storage::disk('public')->delete($originalPath);
                }

                $complaint->attachments()->create([
                    'path' => $finalPath,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
                $attachmentCount++;
            }

            // Update log with actual attachment count
            PrivacyLogger::logComplaintAction('attachments_uploaded', $complaint->id, [
                'attachment_count' => $attachmentCount,
            ]);
        }

        return redirect()->route('complaint.thanks')->with([
            'success' => 'Uw klacht is succesvol ingediend.',
            'complaint_id' => $complaint->id,
        ]);
    }

    public function thanks(): View
    {
        return view('pages.complaint-thanks');
    }

    public function track(): View
    {
        // Validate that both id and email are provided
        $id = request()->input('id');
        $email = request()->input('email');

        if (!$id || !$email) {
            abort(403, 'Klacht ID en e-mailadres zijn verplicht.');
        }

        // Find complaint and verify email
        $complaint = Complaint::with('attachments')->findOrFail($id);

        if ($complaint->reporter_email !== $email) {
            abort(403, 'E-mailadres komt niet overeen met de melding.');
        }

        // Log tracking access (privacy-friendly)
        PrivacyLogger::logComplaintAction('tracked', $complaint->id, [
            'status' => $complaint->status,
        ]);

        return view('pages.complaint-track', compact('complaint'));
    }
}
