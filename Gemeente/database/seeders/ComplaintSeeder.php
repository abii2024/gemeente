<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Complaint;
use App\Models\User;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Maak enkele test klachten
        $complaints = [
            [
                'title' => 'Kapotte straatlantaarn Hoofdstraat',
                'description' => 'De straatlantaarn bij huisnummer 123 op de Hoofdstraat is al sinds vorige week kapot. Dit zorgt voor een onveilige situatie vooral \'s avonds.',
                'status' => 'open',
                'lat' => 52.3676,
                'lng' => 4.9041,
                'reporter_name' => 'Jan Jansen',
                'reporter_email' => 'jan.jansen@email.com',
            ],
            [
                'title' => 'Gat in de weg Parkstraat',
                'description' => 'Er zit een groot gat in de weg op de Parkstraat ter hoogte van nummer 45. Auto\'s moeten uitwijken wat gevaarlijke situaties oplevert.',
                'status' => 'in_behandeling',
                'lat' => 52.3702,
                'lng' => 4.8952,
                'reporter_name' => 'Maria de Vries',
                'reporter_email' => 'maria.devries@email.com',
            ],
            [
                'title' => 'Overlast van hondenpoep Vondelpark',
                'description' => 'In het Vondelpark wordt veel hondenpoep niet opgeruimd door baasjes. Vooral rond de speeltuin is dit een probleem.',
                'status' => 'open',
                'lat' => 52.3584,
                'lng' => 4.8680,
                'reporter_name' => 'Piet Pietersen',
                'reporter_email' => 'piet.pietersen@email.com',
            ],
            [
                'title' => 'Defecte prullenbak Museumplein',
                'description' => 'De prullenbak bij de ingang van het Rijksmuseum hangt scheef en de deksel is kapot. Afval waait weg.',
                'status' => 'opgelost',
                'lat' => 52.3600,
                'lng' => 4.8852,
                'reporter_name' => 'Sophie van der Berg',
                'reporter_email' => 'sophie.vandenberg@email.com',
            ],
            [
                'title' => 'Geluidsoverlast bouwwerkzaamheden',
                'description' => 'De bouwwerkzaamheden aan de Damrak beginnen elke dag al om 06:00 \'s ochtends. Dit is veel te vroeg en zorgt voor geluidsoverlast.',
                'status' => 'open',
                'lat' => 52.3738,
                'lng' => 4.8919,
                'reporter_name' => 'Ahmed Hassan',
                'reporter_email' => 'ahmed.hassan@email.com',
            ],
        ];

        foreach ($complaints as $complaint) {
            Complaint::create($complaint);
        }
    }
}
