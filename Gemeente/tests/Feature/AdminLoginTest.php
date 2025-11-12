<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_with_correct_credentials(): void
    {
        $admin = $this->seedAdminUser();

        // Attempt login
        $response = $this->post('/login', [
            'email' => 'admin@gemeente.nl',
            'password' => 'admin123',
        ]);

        // Should redirect to intended location or dashboard
        $response->assertRedirect();
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_user_exists_in_database(): void
    {
        $seeded = $this->seedAdminUser();
        $user = User::where('email', 'admin@gemeente.nl')->first();

        $this->assertNotNull($user, 'Admin user should exist in database');
        $this->assertEquals('Admin User', $user->name);
        $this->assertTrue(Hash::check('admin123', $user->password), 'Password should match');
    }

    private function seedAdminUser(): User
    {
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@gemeente.nl',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole($role);

        return $admin;
    }
}
