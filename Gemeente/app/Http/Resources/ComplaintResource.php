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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'location' => [
                'lat' => $this->lat,
                'lng' => $this->lng,
            ],
            'reporter' => [
                'name' => $this->reporter_name,
                'email' => $this->reporter_email,
            ],
            'attachments' => $this->whenLoaded('attachments', function () {
                return $this->attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'path' => $attachment->path,
                        'url' => asset('storage/' . $attachment->path),
                        'mime_type' => $attachment->mime_type,
                        'size' => $attachment->size,
                        'is_image' => $attachment->isImage(),
                    ];
                });
            }),
            'notes' => $this->whenLoaded('notes', function () {
                return $this->notes->map(function ($note) {
                    return [
                        'id' => $note->id,
                        'body' => $note->body,
                        'author' => $note->user->name,
                        'created_at' => $note->created_at,
                    ];
                });
            }),
            'status_history' => $this->whenLoaded('statusHistories', function () {
                return $this->statusHistories->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'from_status' => $history->from_status,
                        'to_status' => $history->to_status,
                        'changed_by' => $history->user ? $history->user->name : 'Systeem',
                        'changed_at' => $history->created_at,
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
