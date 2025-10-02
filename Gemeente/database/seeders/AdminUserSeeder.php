<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@gemeente.nl',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('admin');

        // Create medewerker user
        $medewerker = User::create([
            'name' => 'Jan Medewerker',
            'email' => 'medewerker@gemeente.nl',
            'password' => Hash::make('medewerker123'),
            'email_verified_at' => now(),
        ]);

        $medewerker->assignRole('medewerker');

        $this->command->info('Admin user created: admin@gemeente.nl / admin123');
        $this->command->info('Medewerker user created: medewerker@gemeente.nl / medewerker123');
    }
}
