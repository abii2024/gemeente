<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Default settings for retention and notification intervals
        Setting::setValue(
            'retention_days',
            365,
            'integer',
            'Aantal dagen dat klachten bewaard blijven in het systeem'
        );

        Setting::setValue(
            'notification_interval_hours',
            24,
            'integer',
            'Interval in uren tussen automatische notificaties over openstaande klachten'
        );

        Setting::setValue(
            'auto_assign_new_complaints',
            false,
            'boolean',
            'Automatisch nieuwe klachten toewijzen aan beschikbare medewerkers'
        );

        Setting::setValue(
            'email_notifications_enabled',
            true,
            'boolean',
            'E-mail notificaties ingeschakeld voor statusupdates'
        );

        Setting::setValue(
            'max_file_upload_size_mb',
            10,
            'integer',
            'Maximale bestandsgrootte voor uploads in MB'
        );
    }
}
