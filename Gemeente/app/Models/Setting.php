<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    protected $casts = [
        'value' => 'json',
    ];

    // Helper methods for common settings
    public static function getRetentionDays(): int
    {
        $setting = self::where('key', 'retention_days')->first();
        return $setting ? (int) $setting->value : 365; // Default 1 year
    }

    public static function getNotificationInterval(): int
    {
        $setting = self::where('key', 'notification_interval_hours')->first();
        return $setting ? (int) $setting->value : 24; // Default 24 hours
    }

    public static function setValue(string $key, mixed $value, string $type = 'string', string $description = ''): void
    {
        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
            ]
        );
    }
}
