<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Complaint;
use App\Services\PrivacyLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_admin_pages()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function non_admin_users_cannot_access_admin_pages()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_users_can_access_admin_pages()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_pages_have_no_index_headers()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        
        $response->assertHeader('X-Robots-Tag', 'noindex');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Cache-Control', 'no-cache, private, no-store, must-revalidate, max-stale=0, post-check=0, pre-check=0');
    }

    /** @test */
    public function complaint_form_validates_required_fields()
    {
        $response = $this->post('/complaint', []);
        
        $response->assertSessionHasErrors([
            'title',
            'description',
            'category',
            'reporter_name',
            'reporter_email'
        ]);
    }

    /** @test */
    public function privacy_logger_sanitizes_pii_data()
    {
        // Capture log output
        Log::shouldReceive('channel')->with('privacy_safe')->andReturnSelf();
        Log::shouldReceive('info')->once()->with(
            'User action: test_action',
            $this->callback(function ($context) {
                // Should not contain PII
                $this->assertArrayNotHasKey('email', $context);
                $this->assertArrayNotHasKey('name', $context);
                $this->assertArrayNotHasKey('phone', $context);
                
                // Should contain hashed IP
                $this->assertArrayHasKey('ip_hash', $context);
                
                // Should contain safe data
                $this->assertArrayHasKey('category', $context);
                $this->assertEquals('test_category', $context['category']);
                
                return true;
            })
        );

        // Test the privacy logger
        PrivacyLogger::logUserAction('test_action', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'phone' => '123456789',
            'ip' => '192.168.1.1',
            'category' => 'test_category',
            'safe_data' => 'this is safe'
        ]);
    }

    /** @test */
    public function csrf_protection_is_enabled_on_forms()
    {
        $response = $this->post('/complaint', [
            'title' => 'Test Complaint',
            'description' => 'Test Description',
            'category' => 'andere',
            'reporter_name' => 'Test User',
            'reporter_email' => 'test@example.com'
        ]);

        // Should fail without CSRF token
        $response->assertStatus(419);
    }

    /** @test */
    public function file_uploads_are_validated()
    {
        $response = $this->post('/complaint', [
            'title' => 'Test Complaint',
            'description' => 'Test Description',
            'category' => 'andere',
            'reporter_name' => 'Test User',
            'reporter_email' => 'test@example.com',
            'attachments' => [
                // Try to upload invalid file type
                \Illuminate\Http\UploadedFile::fake()->create('malicious.exe', 1000)
            ]
        ]);

        $response->assertSessionHasErrors(['attachments.0']);
    }

    /** @test */
    public function sensitive_routes_require_authentication()
    {
        $sensitiveRoutes = [
            '/admin/dashboard',
            '/admin/complaints',
            '/admin/database',
        ];

        foreach ($sensitiveRoutes as $route) {
            $response = $this->get($route);
            $this->assertTrue(
                $response->isRedirection() || $response->status() === 401 || $response->status() === 403,
                "Route {$route} should require authentication"
            );
        }
    }
}