<?php

namespace Database\Seeders;

use App\Models\Complaint;
use Illuminate\Database\Seeder;

class ComplaintCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Amsterdam coordinates for testing
        $amsterdamLocations = [
            ['name' => 'Dam Square', 'lat' => 52.3730, 'lng' => 4.8924],
            ['name' => 'Centraal Station', 'lat' => 52.3791, 'lng' => 4.9003],
            ['name' => 'Museumplein', 'lat' => 52.3579, 'lng' => 4.8826],
            ['name' => 'Vondelpark', 'lat' => 52.3579, 'lng' => 4.8686],
            ['name' => 'Leidseplein', 'lat' => 52.3642, 'lng' => 4.8829],
            ['name' => 'Rembrandtplein', 'lat' => 52.3664, 'lng' => 4.8955],
            ['name' => 'Waterlooplein', 'lat' => 52.3677, 'lng' => 4.9025],
            ['name' => 'Jordaan', 'lat' => 52.3799, 'lng' => 4.8791],
            ['name' => 'De Pijp', 'lat' => 52.3512, 'lng' => 4.8910],
            ['name' => 'Oost', 'lat' => 52.3602, 'lng' => 4.9299],
        ];

        $categories = ['Onderhoud', 'Openbare Ruimte', 'Verkeer', 'Overlast', 'Groen', 'Afval'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $statuses = ['open', 'in_progress', 'resolved', 'closed'];

        // Update existing complaints with coordinates
        $complaints = Complaint::whereNull('lat')->orWhereNull('lng')->get();

        foreach ($complaints as $index => $complaint) {
            $location = $amsterdamLocations[$index % count($amsterdamLocations)];

            // Add some randomness to coordinates (within ~500m radius)
            $latOffset = (rand(-50, 50) / 10000);
            $lngOffset = (rand(-50, 50) / 10000);

            $complaint->update([
                'lat' => $location['lat'] + $latOffset,
                'lng' => $location['lng'] + $lngOffset,
                'location' => $location['name'] . ', Amsterdam',
            ]);
        }

        // Create some new complaints with coordinates if there are less than 20
        $currentCount = Complaint::count();
        if ($currentCount < 20) {
            for ($i = $currentCount; $i < 20; $i++) {
                $location = $amsterdamLocations[$i % count($amsterdamLocations)];
                $latOffset = (rand(-50, 50) / 10000);
                $lngOffset = (rand(-50, 50) / 10000);

                Complaint::create([
                    'title' => 'Test Klacht ' . ($i + 1),
                    'description' => 'Dit is een test klacht om de kaart functionaliteit te demonstreren. Locatie: ' . $location['name'],
                    'category' => $categories[array_rand($categories)],
                    'priority' => $priorities[array_rand($priorities)],
                    'status' => $statuses[array_rand($statuses)],
                    'location' => $location['name'] . ', Amsterdam',
                    'lat' => $location['lat'] + $latOffset,
                    'lng' => $location['lng'] + $lngOffset,
                    'reporter_name' => 'Test Gebruiker ' . ($i + 1),
                    'reporter_email' => 'test' . ($i + 1) . '@example.com',
                    'reporter_phone' => '06-12345678',
                ]);
            }
        }

        $this->command->info('Complaint coordinates seeded successfully!');
    }
}
