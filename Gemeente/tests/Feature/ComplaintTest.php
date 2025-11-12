<?php

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function createUserWithRole(string $role): User
{
    $roleModel = Role::firstOrCreate([
        'name' => $role,
        'guard_name' => 'web',
    ]);

    $user = User::factory()->create();
    $user->assignRole($roleModel);

    return $user;
}

describe('Complaint Submission', function () {
    test('guest can view complaint form', function () {
        $response = $this->get(route('complaint.create'));

        $response->assertStatus(200);
        $response->assertSee('Klacht of Melding Indienen');
    });

    test('guest can submit complaint with required fields', function () {
        Storage::fake('public');

        $complaintData = [
            'title' => 'Kapotte straatverlichting',
            'description' => 'De straatverlichting op de hoek is al 3 dagen kapot.',
            'lat' => 52.3676,
            'lng' => 4.9041,
            'category' => 'openbare_verlichting',
            'reporter_name' => 'Jan de Vries',
            'reporter_email' => 'jan@example.com',
        ];

        $response = $this->post(route('complaint.store'), $complaintData);

        $response->assertRedirect(route('complaint.thanks'));
        $this->assertDatabaseHas('complaints', [
            'title' => 'Kapotte straatverlichting',
            'category' => 'openbare_verlichting',
            'status' => 'open',
        ]);
    });

    test('complaint requires title', function () {
        $response = $this->post(route('complaint.store'), [
            'description' => 'Test description',
            'lat' => 52.3676,
            'lng' => 4.9041,
            'category' => 'afval',
            'reporter_name' => 'Jan de Vries',
            'reporter_email' => 'jan@example.com',
        ]);

        $response->assertSessionHasErrors(['title']);
    });

    test('complaint requires valid email', function () {
        $response = $this->post(route('complaint.store'), [
            'title' => 'Test',
            'description' => 'Test description',
            'lat' => 52.3676,
            'lng' => 4.9041,
            'category' => 'afval',
            'reporter_name' => 'Jan de Vries',
            'reporter_email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors(['reporter_email']);
    });

    test('complaint can include photo attachment', function () {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('complaint.jpg', 1920, 1080);

        $response = $this->post(route('complaint.store'), [
            'title' => 'Test met foto',
            'description' => 'Beschrijving',
            'lat' => 52.3676,
            'lng' => 4.9041,
            'category' => 'afval',
            'reporter_name' => 'Jan de Vries',
            'reporter_email' => 'jan@example.com',
            'attachments' => [$file],
        ]);

        $response->assertRedirect(route('complaint.thanks'));
        $this->assertDatabaseHas('complaints', ['title' => 'Test met foto']);

        $complaint = Complaint::where('title', 'Test met foto')->first();
        expect($complaint->attachments)->toHaveCount(1);
    });

    test('complaint validates GPS coordinates', function () {
        $response = $this->post(route('complaint.store'), [
            'title' => 'Test',
            'description' => 'Test description',
            'lat' => 'invalid',
            'lng' => 4.9041,
            'category' => 'afval',
            'reporter_name' => 'Jan de Vries',
            'reporter_email' => 'jan@example.com',
        ]);

        $response->assertSessionHasErrors(['lat']);
    });

    test('thanks page shows complaint number', function () {
        session(['complaint_id' => 12345]);

        $response = $this->get(route('complaint.thanks'));

        $response->assertStatus(200);
        $response->assertSee('12345');
    });
});

describe('Admin Complaint Management', function () {
    test('guest cannot access admin dashboard', function () {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('login'));
    });

    test('regular user cannot access admin dashboard', function () {
        $user = createUserWithRole('user');

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
    });

    test('admin can view dashboard', function () {
        $admin = createUserWithRole('admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    });

    test('admin can see recent complaints on dashboard', function () {
        $admin = createUserWithRole('admin');
        $complaints = Complaint::factory()->count(7)->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        // Should only show 5 most recent
        $response->assertViewHas('recent_complaints', function ($viewComplaints) {
            return count($viewComplaints) === 5;
        });
    });

    test('admin can view individual complaint', function () {
        $admin = createUserWithRole('admin');
        $complaint = Complaint::factory()->create([
            'title' => 'Test Klacht',
            'status' => 'open',
        ]);

        $response = $this->actingAs($admin)->get("/admin/complaints/{$complaint->id}");

        $response->assertStatus(200);
        $response->assertSee('Test Klacht');
    });

    test('admin can update complaint status to resolved', function () {
        $admin = createUserWithRole('admin');
        $complaint = Complaint::factory()->create(['status' => 'open']);

        $response = $this->actingAs($admin)
            ->patch("/admin/complaints/{$complaint->id}/status", [
                'status' => 'opgelost',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'status' => 'opgelost',
        ]);
    });

    test('admin can delete complaint', function () {
        $admin = createUserWithRole('admin');
        $complaint = Complaint::factory()->create();

        $response = $this->actingAs($admin)
            ->delete("/admin/complaints/{$complaint->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('complaints', [
            'id' => $complaint->id,
        ]);
    });

    test('admin can add note to complaint', function () {
        $admin = createUserWithRole('admin');
        $complaint = Complaint::factory()->create();

        $response = $this->actingAs($admin)
            ->post("/admin/complaints/{$complaint->id}/notes", [
                'body' => 'Dit is een interne notitie',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('notes', [
            'complaint_id' => $complaint->id,
            'body' => 'Dit is een interne notitie',
        ]);
    });

    test('admin can search complaints by id', function () {
        $admin = createUserWithRole('admin');
        $complaint = Complaint::factory()->create(['title' => 'Zoekbare klacht']);

        $response = $this->actingAs($admin)
            ->get("/admin/complaints?search={$complaint->id}");

        $response->assertStatus(200);
        $response->assertSee('Zoekbare klacht');
    });

    test('admin can view complaints on map', function () {
        $admin = createUserWithRole('admin');
        Complaint::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/admin/complaints/map');

        $response->assertStatus(200);
        $response->assertViewHas('complaints');
    });
});

describe('Diensten Functionality', function () {
    test('guest cannot access diensten pages', function () {
        $response = $this->get(route('diensten.paspoort'));

        $response->assertRedirect(route('login'));
    });

    test('authenticated user can view paspoort page', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('diensten.paspoort'));

        $response->assertStatus(200);
        $response->assertSee('Paspoort Aanvragen');
    });

    test('authenticated user can submit afspraak', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('diensten.afspraak.store'), [
            'dienst' => 'Paspoort Aanvraag',
            'datum' => now()->addDays(2)->format('Y-m-d'),
            'tijd' => '10:00',
            'opmerking' => 'Test opmerking',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('afspraken', [
            'user_id' => $user->id,
            'dienst' => 'Paspoort Aanvraag',
            'status' => 'gepland',
        ]);
    });

    test('afspraak requires future date', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('diensten.afspraak.store'), [
            'dienst' => 'Paspoort Aanvraag',
            'datum' => now()->subDays(1)->format('Y-m-d'), // Past date
            'tijd' => '10:00',
        ]);

        $response->assertSessionHasErrors(['datum']);
    });
});
