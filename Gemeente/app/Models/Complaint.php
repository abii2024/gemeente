<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complaint extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'priority',
        'status',
        'location',
        'lat',
        'lng',
        'reporter_name',
        'reporter_email',
        'reporter_phone',
        'internal_notes',
        'resolved_at',
        'assigned_to',
    ];

    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'resolved_at' => 'datetime',
    ];

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInBehandeling($query)
    {
        return $query->where('status', 'in_behandeling');
    }

    public function scopeOpgelost($query)
    {
        return $query->where('status', 'opgelost');
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helpers
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isInBehandeling(): bool
    {
        return $this->status === 'in_behandeling';
    }

    public function isOpgelost(): bool
    {
        return $this->status === 'opgelost';
    }
}
