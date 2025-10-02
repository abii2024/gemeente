<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $includeSensitive = $request->user() && $request->user()->can('view complaints');

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'location' => [
                'lat' => $this->lat,
                'lng' => $this->lng,
                'description' => $this->location,
            ],
            'reporter' => $this->when($includeSensitive, function () {
                return [
                    'name' => $this->reporter_name,
                    'email' => $this->reporter_email,
                    'phone' => $this->reporter_phone,
                ];
            }),
            'attachments' => $this->whenLoaded('attachments', function () {
                return $this->attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'path' => $attachment->path,
                        'url' => asset('storage/'.$attachment->path),
                        'mime_type' => $attachment->mime,
                        'size' => $attachment->size,
                        'is_image' => $attachment->isImage(),
                    ];
                });
            }),
            'notes' => $this->when($includeSensitive && $this->relationLoaded('notes'), function () {
                return $this->notes->map(function ($note) {
                    return [
                        'id' => $note->id,
                        'body' => $note->body,
                        'author' => $note->user?->name,
                        'created_at' => $note->created_at,
                    ];
                });
            }),
            'status_history' => $this->when($includeSensitive && $this->relationLoaded('statusHistories'), function () {
                return $this->statusHistories->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'from_status' => $history->from,
                        'to_status' => $history->to,
                        'changed_by' => $history->user?->name ?? 'Systeem',
                        'changed_at' => $history->created_at,
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
