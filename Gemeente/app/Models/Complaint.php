<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complaint extends Model
{
    use HasFactory;

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

    /**
     * @return HasMany<Attachment>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * @return HasMany<Note>
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * @return HasMany<StatusHistory>
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class);
    }

    // Scopes
    public function scopeOpen($query): void
    {
        $query->where('status', 'open');
    }

    public function scopeInBehandeling($query): void
    {
        $query->where('status', 'in_behandeling');
    }

    public function scopeOpgelost($query): void
    {
        $query->where('status', 'opgelost');
    }

    public function scopeRecent($query, int $days = 7): void
    {
        $query->where('created_at', '>=', now()->subDays($days));
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
