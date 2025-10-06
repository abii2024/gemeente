#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "\n=== DEBUG LOGIN ISSUE ===\n\n";

// Get user
$user = User::where('email', 'admin@gemeente.nl')->first();

if (!$user) {
    echo "❌ USER NOT FOUND\n";
    echo "Run: php artisan db:seed --class=AdminUserSeeder\n";
    exit(1);
}

echo "✅ User found:\n";
echo "   ID: {$user->id}\n";
echo "   Name: {$user->name}\n";
echo "   Email: {$user->email}\n";
echo "   Email Verified: " . ($user->email_verified_at ? 'Yes' : 'No') . "\n";
echo "   Created: {$user->created_at}\n\n";

// Check password
$testPassword = 'admin123';
$passwordCheck = Hash::check($testPassword, $user->password);

echo "🔐 Password Check:\n";
echo "   Testing password: {$testPassword}\n";
echo "   Result: " . ($passwordCheck ? '✅ CORRECT' : '❌ INCORRECT') . "\n\n";

// Check roles
$roles = $user->roles;
echo "👤 Roles ({$roles->count()}):\n";
if ($roles->isEmpty()) {
    echo "   ❌ NO ROLES ASSIGNED!\n";
    echo "   Run: php artisan tinker --execute=\"App\\Models\\User::find({$user->id})->assignRole('admin');\"\n";
} else {
    foreach ($roles as $role) {
        echo "   ✅ {$role->name}\n";
    }
}

// Check permissions
$permissions = $user->getAllPermissions();
echo "\n🔑 Permissions ({$permissions->count()}):\n";
foreach ($permissions as $permission) {
    echo "   - {$permission->name}\n";
}

// Check if can access admin
$hasAdminRole = $user->hasRole('admin');
echo "\n🚪 Admin Access:\n";
echo "   Has admin role: " . ($hasAdminRole ? '✅ YES' : '❌ NO') . "\n";

if ($hasAdminRole) {
    echo "\n✅ USER CAN LOGIN AS ADMIN\n";
    echo "\nLogin at: http://127.0.0.1:8000/login\n";
    echo "Email: admin@gemeente.nl\n";
    echo "Password: admin123\n";
} else {
    echo "\n❌ USER CANNOT ACCESS ADMIN PANEL\n";
    echo "Fix: User needs 'admin' role\n";
}

echo "\n";
